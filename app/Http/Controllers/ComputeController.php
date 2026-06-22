<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ComputeInstance;
use App\Models\ActivityLog;

class ComputeController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $computeSub = $user->getOrCreateComputeSub();
        $instances  = ComputeInstance::where('user_id', $user->id)->latest()->get();

        $runningCount   = $instances->where('status', 'running')->count();
        $stoppedCount   = $instances->where('status', 'stopped')->count();
        $totalVcpu      = $instances->where('status', 'running')->sum('vcpu');
        $totalRam       = $instances->where('status', 'running')->sum('ram_gb');

        return view('compute.index', compact(
            'computeSub', 'instances',
            'runningCount', 'stoppedCount',
            'totalVcpu', 'totalRam'
        ));
    }

    public function create()
    {
        $user       = Auth::user();
        $computeSub = $user->getOrCreateComputeSub();

        // Tipe instance yang tersedia
        $instanceTypes = [
            ['id' => 'nano',    'name' => 'Nano',    'vcpu' => 1, 'ram' => 1,  'desc' => 'Dev & testing'],
            ['id' => 'micro',   'name' => 'Micro',   'vcpu' => 1, 'ram' => 2,  'desc' => 'Web kecil'],
            ['id' => 'small',   'name' => 'Small',   'vcpu' => 2, 'ram' => 4,  'desc' => 'App ringan'],
            ['id' => 'medium',  'name' => 'Medium',  'vcpu' => 2, 'ram' => 8,  'desc' => 'Produksi'],
            ['id' => 'large',   'name' => 'Large',   'vcpu' => 4, 'ram' => 16, 'desc' => 'High traffic'],
            ['id' => 'xlarge',  'name' => 'XLarge',  'vcpu' => 8, 'ram' => 32, 'desc' => 'Enterprise'],
        ];

        $osImages = [
            ['id' => 'ubuntu-22.04', 'name' => 'Ubuntu 22.04 LTS', 'icon' => '🐧'],
            ['id' => 'ubuntu-20.04', 'name' => 'Ubuntu 20.04 LTS', 'icon' => '🐧'],
            ['id' => 'debian-12',    'name' => 'Debian 12',         'icon' => '🌀'],
            ['id' => 'centos-9',     'name' => 'CentOS Stream 9',   'icon' => '🎩'],
            ['id' => 'alpine-3.18',  'name' => 'Alpine Linux 3.18', 'icon' => '🏔'],
            ['id' => 'windows-2022', 'name' => 'Windows Server 2022','icon' => '🪟'],
        ];

        return view('compute.create', compact('computeSub', 'instanceTypes', 'osImages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z0-9\-_]+$/'],
            'instance_type' => ['required', 'string'],
            'os_image'      => ['required', 'string'],
        ]);

        $user       = Auth::user();
        $computeSub = $user->getOrCreateComputeSub();

        // Cek batas vCPU dari subscription
        $usedVcpu = ComputeInstance::where('user_id', $user->id)
            ->where('status', 'running')->sum('vcpu');

        $vcpuMap = ['nano'=>1,'micro'=>1,'small'=>2,'medium'=>2,'large'=>4,'xlarge'=>8];
        $ramMap  = ['nano'=>1,'micro'=>2,'small'=>4,'medium'=>8,'large'=>16,'xlarge'=>32];

        $newVcpu = $vcpuMap[$request->instance_type] ?? 1;

        if (($usedVcpu + $newVcpu) > $computeSub->vcpu_limit) {
            return back()->with('error', "Batas vCPU paket Anda ({$computeSub->vcpu_limit} vCPU) akan terlampaui. Silakan upgrade.");
        }

        $usedRam = ComputeInstance::where('user_id', $user->id)
            ->where('status', 'running')->sum('ram_gb');
        $newRam = $ramMap[$request->instance_type] ?? 1;

        if (($usedRam + $newRam) > $computeSub->ram_go) {
            return back()->with('error', "Batas RAM paket Anda ({$computeSub->ram_go} GB) akan terlampaui. Silakan upgrade.");
        }

        $instance = ComputeInstance::create([
            'user_id'         => $user->id,
            'subscription_id' => $computeSub->id,
            'name'            => $request->name,
            'instance_type'   => $request->instance_type,
            'vcpu'            => $newVcpu,
            'ram_gb'          => $ramMap[$request->instance_type] ?? 1,
            'os_image'        => $request->os_image,
            'ip_address'      => '10.' . rand(0,255) . '.' . rand(0,255) . '.' . rand(1,254),
            'status'          => 'running',
            'started_at'      => now(),
        ]);

        ActivityLog::create([
            'user_id'       => $user->id,
            'action'        => 'Launch Instance',
            'resource_type' => 'Compute',
            'resource_name' => $instance->name,
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => $request->ip(),
            'metadata'      => ['instance_type' => $instance->instance_type, 'os' => $instance->os_image],
        ]);

        return redirect()->route('compute.index')
            ->with('success', "Instance \"{$instance->name}\" berhasil diluncurkan!");
    }

    public function destroy(ComputeInstance $instance)
    {
        if ($instance->user_id !== Auth::id()) abort(403);

        $name = $instance->name;
        $instance->update(['status' => 'terminated', 'stopped_at' => now()]);
        $instance->delete();

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => 'Terminate Instance',
            'resource_type' => 'Compute',
            'resource_name' => $name,
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => request()->ip(),
            'metadata'      => [],
        ]);

        return back()->with('success', "Instance \"{$name}\" berhasil dihentikan.");
    }

    public function toggleStatus(ComputeInstance $instance)
    {
        if ($instance->user_id !== Auth::id()) abort(403);

        $newStatus = $instance->status === 'running' ? 'stopped' : 'running';
        $instance->update([
            'status'     => $newStatus,
            'started_at' => $newStatus === 'running' ? now() : $instance->started_at,
            'stopped_at' => $newStatus === 'stopped' ? now() : null,
        ]);

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'action'        => $newStatus === 'running' ? 'Start Instance' : 'Stop Instance',
            'resource_type' => 'Compute',
            'resource_name' => $instance->name,
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => request()->ip(),
            'metadata'      => ['new_status' => $newStatus],
        ]);

        return back()->with('success', "Instance \"{$instance->name}\" berhasil " . ($newStatus === 'running' ? 'dinyalakan' : 'dimatikan') . '.');
    }
}

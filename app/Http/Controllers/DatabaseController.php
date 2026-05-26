<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class DatabaseController extends Controller
{
    // Simulasi data database instances (belum ada tabel di ERD, jadi hardcode + session)
    private function getDatabases()
    {
        return session('user_databases_' . Auth::id(), []);
    }

    public function index()
    {
        $user      = Auth::user();
        $storageSub = $user->getOrCreateStorageSub();
        $databases = $this->getDatabases();

        $runningCount = count(array_filter($databases, fn($d) => $d['status'] === 'available'));
        $totalStorage = array_sum(array_column($databases, 'storage_gb'));

        return view('database.index', compact('storageSub', 'databases', 'runningCount', 'totalStorage'));
    }

    public function create()
    {
        $engines = [
            ['id' => 'mysql-8.0',      'name' => 'MySQL 8.0',        'icon' => '🐬', 'desc' => 'Paling populer untuk web'],
            ['id' => 'mysql-5.7',      'name' => 'MySQL 5.7',        'icon' => '🐬', 'desc' => 'Kompatibilitas lama'],
            ['id' => 'postgresql-16',  'name' => 'PostgreSQL 16',    'icon' => '🐘', 'desc' => 'ACID compliance terbaik'],
            ['id' => 'postgresql-15',  'name' => 'PostgreSQL 15',    'icon' => '🐘', 'desc' => 'Stable & production-ready'],
            ['id' => 'mariadb-10.11',  'name' => 'MariaDB 10.11',   'icon' => '🦭', 'desc' => 'Open source MySQL fork'],
            ['id' => 'redis-7',        'name' => 'Redis 7',          'icon' => '🔴', 'desc' => 'In-memory cache & queue'],
        ];

        $sizes = [
            ['id' => 'db.nano',   'name' => 'Nano',   'vcpu' => 1, 'ram' => 1,  'storage' => 20,  'price' => 0],
            ['id' => 'db.micro',  'name' => 'Micro',  'vcpu' => 1, 'ram' => 2,  'storage' => 50,  'price' => 35000],
            ['id' => 'db.small',  'name' => 'Small',  'vcpu' => 2, 'ram' => 4,  'storage' => 100, 'price' => 75000],
            ['id' => 'db.medium', 'name' => 'Medium', 'vcpu' => 2, 'ram' => 8,  'storage' => 200, 'price' => 150000],
            ['id' => 'db.large',  'name' => 'Large',  'vcpu' => 4, 'ram' => 16, 'storage' => 500, 'price' => 300000],
        ];

        return view('database.create', compact('engines', 'sizes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z0-9\-_]+$/'],
            'engine'     => ['required', 'string'],
            'db_size'    => ['required', 'string'],
            'db_name'    => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9_]+$/'],
            'db_user'    => ['required', 'string', 'max:32'],
            'db_password'=> ['required', 'string', 'min:8'],
        ]);

        $user = Auth::user();

        $sizeMap = [
            'db.nano'   => ['vcpu' => 1, 'ram' => 1,  'storage' => 20],
            'db.micro'  => ['vcpu' => 1, 'ram' => 2,  'storage' => 50],
            'db.small'  => ['vcpu' => 2, 'ram' => 4,  'storage' => 100],
            'db.medium' => ['vcpu' => 2, 'ram' => 8,  'storage' => 200],
            'db.large'  => ['vcpu' => 4, 'ram' => 16, 'storage' => 500],
        ];

        $size = $sizeMap[$request->db_size] ?? $sizeMap['db.nano'];

        $databases   = $this->getDatabases();
        $databases[] = [
            'id'         => uniqid('db_'),
            'name'       => $request->name,
            'engine'     => $request->engine,
            'db_name'    => $request->db_name,
            'db_user'    => $request->db_user,
            'db_size'    => $request->db_size,
            'vcpu'       => $size['vcpu'],
            'ram_gb'     => $size['ram'],
            'storage_gb' => $size['storage'],
            'status'     => 'available',
            'endpoint'   => strtolower($request->name) . '.db.ministack.local',
            'port'       => str_contains($request->engine, 'redis') ? 6379 : (str_contains($request->engine, 'postgresql') ? 5432 : 3306),
            'created_at' => now()->toDateTimeString(),
        ];

        session(['user_databases_' . $user->id => $databases]);

        ActivityLog::create([
            'user_id'       => $user->id,
            'action'        => 'Create Database',
            'resource_type' => 'Database',
            'resource_name' => $request->name,
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => $request->ip(),
            'metadata'      => ['engine' => $request->engine, 'size' => $request->db_size],
        ]);

        return redirect()->route('database.index')
            ->with('success', "Database \"{$request->name}\" berhasil dibuat!");
    }

    public function destroy(string $id)
    {
        $user      = Auth::user();
        $databases = $this->getDatabases();
        $db        = collect($databases)->firstWhere('id', $id);

        if (! $db) abort(404);

        $databases = array_values(array_filter($databases, fn($d) => $d['id'] !== $id));
        session(['user_databases_' . $user->id => $databases]);

        ActivityLog::create([
            'user_id'       => $user->id,
            'action'        => 'Delete Database',
            'resource_type' => 'Database',
            'resource_name' => $db['name'],
            'device_type'   => 'web',
            'status'        => 'success',
            'ip_address'    => request()->ip(),
            'metadata'      => [],
        ]);

        return back()->with('success', "Database \"{$db['name']}\" berhasil dihapus.");
    }
}

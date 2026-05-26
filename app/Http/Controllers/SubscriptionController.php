<?php

namespace App\Http\Controllers;

use App\Models\StorageSubscription;
use App\Models\ComputeSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Tampilkan halaman daftar paket langganan beserta info langganan aktif user.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $storageSub  = $user->getOrCreateStorageSub();
        $computeSub  = $user->getOrCreateComputeSub();

        // ── Storage metrics ───────────────────────────────────────
        $storageQuota     = $storageSub->quota_gb ?? 5;
        $storageUsedBytes = $user->storageBuckets()->sum('size_bytes') ?? 0;
        $storageUsed      = round($storageUsedBytes / 1073741824, 2); // bytes → GB
        $storagePercent   = $storageQuota > 0
            ? min(100, round(($storageUsed / $storageQuota) * 100))
            : 0;

        // ── Bucket metrics ────────────────────────────────────────
        $bucketLimit   = $storageSub->bucket_limit ?? 3;
        $totalBuckets  = $user->storageBuckets()->count();
        $bucketPercent = $bucketLimit > 0
            ? min(100, round(($totalBuckets / $bucketLimit) * 100))
            : 0;

        // ── Access Key metrics ────────────────────────────────────
        $keyLimit      = $storageSub->bucket_limit ?? 2; // pakai bucket_limit sebagai proxy key limit
        $totalKeys     = $user->credentials()->where('is_active', true)->count();
        $keyPercent    = $keyLimit > 0
            ? min(100, round(($totalKeys / $keyLimit) * 100))
            : 0;

        // ── Compute metrics ───────────────────────────────────────
        $computeLimit   = $computeSub->compute_units ?? 100;
        $computeUsed    = $user->computeInstances()->where('status', 'running')->count();
        $computePercent = $computeLimit > 0
            ? min(100, round(($computeUsed / $computeLimit) * 100))
            : 0;

        return view('subscriptions.index', compact(
            'storageSub',
            'computeSub',
            'storageQuota',
            'storageUsed',
            'storagePercent',
            'bucketLimit',
            'totalBuckets',
            'bucketPercent',
            'keyLimit',
            'totalKeys',
            'keyPercent',
            'computeLimit',
            'computeUsed',
            'computePercent',
        ));
    }

    /**
     * Tampilkan halaman konfirmasi checkout untuk paket storage.
     */
    public function checkout(string $plan)
    {
        $plans = StorageSubscription::availablePlans();

        if (! array_key_exists($plan, $plans)) {
            abort(404, 'Paket tidak ditemukan.');
        }

        $selectedPlan = $plans[$plan];

        return view('subscriptions.checkout', compact('selectedPlan', 'plan'));
    }

    /**
     * Proses pembelian / pergantian paket storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan' => ['required', 'string', 'in:' . implode(',', array_keys(StorageSubscription::availablePlans()))],
        ]);

        $plans    = StorageSubscription::availablePlans();
        $planKey  = $request->input('plan');
        $planData = $plans[$planKey];

        /** @var User $user */
        $user = Auth::user();

        $expiresAt = $planData['price'] > 0
            ? Carbon::now()->addMonth()
            : null;

        // Nonaktifkan langganan storage sebelumnya
        $user->storageSubscriptions()->where('is_active', true)->update(['is_active' => false]);

        // Buat langganan baru
        $storageSub = StorageSubscription::create([
            'user_id'      => $user->id,
            'plan'         => $planKey,
            'quota_gb'     => $planData['quota_gb'],
            'bucket_limit' => $planData['bucket_limit'],
            'price'        => $planData['price'],
            'is_active'    => true,
            'expires_at'   => $expiresAt,
        ]);

        return redirect()
            ->route('subscriptions.index')
            ->with('success', "Langganan paket {$planData['name']} berhasil diaktifkan!");
    }

    /**
     * Batalkan langganan storage aktif user (downgrade ke Free).
     */
    public function cancel()
    {
        /** @var User $user */
        $user       = Auth::user();
        $storageSub = $user->getOrCreateStorageSub();

        if (! $storageSub) {
            return redirect()
                ->route('subscriptions.index')
                ->with('info', 'Tidak ada langganan aktif untuk dibatalkan.');
        }

        if ($storageSub->plan === 'free') {
            return redirect()
                ->route('subscriptions.index')
                ->with('info', 'Paket Free tidak dapat dibatalkan.');
        }

        // Nonaktifkan lalu buat langganan free baru
        $storageSub->update(['is_active' => false]);

        $freePlan = StorageSubscription::availablePlans()['free'];

        StorageSubscription::create([
            'user_id'      => $user->id,
            'plan'         => 'free',
            'quota_gb'     => $freePlan['quota_gb'],
            'bucket_limit' => $freePlan['bucket_limit'],
            'price'        => 0,
            'is_active'    => true,
            'expires_at'   => null,
        ]);

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Langganan berhasil dibatalkan. Akun Anda kini menggunakan paket Free.');
    }
}

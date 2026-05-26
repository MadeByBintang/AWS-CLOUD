<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
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
        $user         = Auth::user();
        $subscription = $user->subscription ?? null;

        // ── Usage metrics ────────────────────────────────────────

        // Storage: kolom size_bytes di tabel storage_buckets, konversi ke GB
        $storageQuota     = $subscription->storage_quota_gb ?? 5;
        $storageUsedBytes = $user->storageBuckets()->sum('size_bytes') ?? 0;
        $storageUsed      = round($storageUsedBytes / 1073741824, 2); // bytes → GB
        $storagePercent   = $storageQuota > 0
            ? min(100, round(($storageUsed / $storageQuota) * 100))
            : 0;

        // Bucket: relasi storageBuckets()
        $bucketLimit   = $subscription->bucket_limit ?? 2;
        $totalBuckets  = $user->storageBuckets()->count();
        $bucketPercent = $bucketLimit > 0
            ? min(100, round(($totalBuckets / $bucketLimit) * 100))
            : 0;

        // Access Keys: relasi credentials()
        $keyLimit      = $subscription->key_limit ?? 1;
        $totalKeys     = $user->credentials()->count();
        $keyPercent    = $keyLimit > 0
            ? min(100, round(($totalKeys / $keyLimit) * 100))
            : 0;

        // Compute: belum ada tabel log, hardcode 0 sampai tabel tersedia
        $computeLimit   = $subscription->compute_units ?? 10;
        $computeUsed    = 0;
        $computePercent = 0;

        return view('subscriptions.index', compact(
            'subscription',
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
     * Tampilkan halaman konfirmasi checkout untuk paket tertentu.
     */
    public function checkout(string $plan)
    {
        $plans = Subscription::availablePlans();

        if (! array_key_exists($plan, $plans)) {
            abort(404, 'Paket tidak ditemukan.');
        }

        $selectedPlan = $plans[$plan];

        return view('subscriptions.checkout', compact('selectedPlan', 'plan'));
    }

    /**
     * Proses pembelian / pergantian paket.
     * Dalam implementasi nyata, integrasikan payment gateway di sini
     * sebelum memanggil store().
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan' => ['required', 'string', 'in:' . implode(',', array_keys(Subscription::availablePlans()))],
        ]);

        $plans    = Subscription::availablePlans();
        $planKey  = $request->input('plan');
        $planData = $plans[$planKey];

        /** @var User $user */
        $user = Auth::user();

        // Hitung tanggal kedaluwarsa (1 bulan dari sekarang, Free = null)
        $expiresAt = $planData['price'] > 0
            ? Carbon::now()->addMonth()
            : null;

        // Upsert: update langganan jika sudah ada, buat baru jika belum
        $subscription = Subscription::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($planData, [
                'user_id'    => $user->id,
                'expires_at' => $expiresAt,
            ])
        );

        return redirect()
            ->route('subscriptions.index')
            ->with('success', "Langganan paket {$subscription->plan_name} berhasil diaktifkan!");
    }

    /**
     * Batalkan langganan aktif user (downgrade ke Free).
     */
    public function cancel()
    {
        /** @var User $user */
        $user         = Auth::user();
        $subscription = $user->subscription;

        if (! $subscription) {
            return redirect()
                ->route('subscriptions.index')
                ->with('info', 'Tidak ada langganan aktif untuk dibatalkan.');
        }

        if ($subscription->plan_name === 'Free') {
            return redirect()
                ->route('subscriptions.index')
                ->with('info', 'Paket Free tidak dapat dibatalkan.');
        }

        // Downgrade ke Free
        $freePlan = Subscription::availablePlans()['free'];

        $subscription->update(array_merge($freePlan, [
            'expires_at' => null,
        ]));

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Langganan berhasil dibatalkan. Akun Anda kini menggunakan paket Free.');
    }
}

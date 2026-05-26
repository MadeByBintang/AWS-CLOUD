<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\StorageBucket;
use App\Models\Credential;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ── Langganan aktif ───────────────────────────────────────
        $storageSub = $user->getOrCreateStorageSub();
        $computeSub = $user->getOrCreateComputeSub();

        // ── Storage metrics ───────────────────────────────────────
        $storageQuota   = $storageSub?->quota_gb ?? 5;
        $storageUsed    = round($user->storageBuckets()->sum('size_bytes') / 1073741824, 2);
        $storagePercent = $storageQuota > 0
            ? min(100, round(($storageUsed / $storageQuota) * 100))
            : 0;

        // ── Bucket metrics ────────────────────────────────────────
        $bucketLimit   = $storageSub?->bucket_limit ?? 3;
        $buckets       = StorageBucket::where('user_id', $user->id)
            ->latest()->take(6)->get();
        $totalBuckets  = $buckets->count();
        $bucketPercent = $bucketLimit > 0
            ? min(100, round(($totalBuckets / $bucketLimit) * 100))
            : 0;

        // ── Credential / Access Keys ──────────────────────────────
        $keyLimit         = $storageSub?->bucket_limit ?? 2;
        $totalKeys        = Credential::where('user_id', $user->id)
            ->where('is_active', true)->count();
        $keyPercent       = $keyLimit > 0
            ? min(100, round(($totalKeys / $keyLimit) * 100))
            : 0;
        $latestCredential = Credential::where('user_id', $user->id)
            ->where('is_active', true)->latest()->first();

        // ── Compute metrics ───────────────────────────────────────
        $computeLimit   = $computeSub?->compute_units ?? 100;
        $computeUsed    = $user->computeInstances()->where('status', 'running')->count();
        $computePercent = $computeLimit > 0
            ? min(100, round(($computeUsed / $computeLimit) * 100))
            : 0;

        // ── Activity log ──────────────────────────────────────────
        $recentLogs = ActivityLog::where('user_id', $user->id)
            ->latest()->take(8)->get();

        return view('dashboard', compact(
            'user',
            'storageSub',
            'computeSub',
            'storageUsed',
            'storageQuota',
            'storagePercent',
            'buckets',
            'totalBuckets',
            'bucketLimit',
            'bucketPercent',
            'totalKeys',
            'keyLimit',
            'keyPercent',
            'latestCredential',
            'computeUsed',
            'computeLimit',
            'computePercent',
            'recentLogs'
        ));
    }
}

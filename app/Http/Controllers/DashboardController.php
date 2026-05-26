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
        $user         = Auth::user();
        $subscription = $user->subscription;

        // Data storage
        $storageUsed    = 0;
        $storageQuota   = $subscription?->storage_quota_gb ?? 10;
        $storagePercent = $storageQuota > 0
            ? round(($storageUsed / $storageQuota) * 100)
            : 0;

        // Bucket info
        $bucketLimit   = $subscription?->bucket_limit ?? 5;
        $buckets       = StorageBucket::where('user_id', $user->id)
            ->latest()->take(6)->get();
        $totalBuckets  = $buckets->count();
        $bucketPercent = $bucketLimit > 0
            ? round(($totalBuckets / $bucketLimit) * 100)
            : 0;

        // Credential / Access Keys
        $keyLimit         = $subscription?->key_limit ?? 3;
        $totalKeys        = Credential::where('user_id', $user->id)
            ->where('is_active', true)->count();
        $keyPercent       = $keyLimit > 0
            ? round(($totalKeys / $keyLimit) * 100)
            : 0;
        $latestCredential = Credential::where('user_id', $user->id)
            ->where('is_active', true)->latest()->first();

        // Compute
        $computeLimit   = $subscription?->compute_units ?? 100;
        $computeUsed    = 0;
        $computePercent = 0;

        // Activity log
        $recentLogs = ActivityLog::where('user_id', $user->id)
            ->latest()->take(8)->get();

        return view('dashboard', compact(
            'user',
            'subscription',
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

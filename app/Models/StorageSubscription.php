<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'quota_gb',
        'bucket_limit',
        'price',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    /**
     * Daftar paket storage yang tersedia.
     */
    public static function availablePlans(): array
    {
        return [
            'free' => [
                'name'         => 'Free',
                'price'        => 0,
                'quota_gb'     => 5,
                'bucket_limit' => 3,
            ],
            'starter' => [
                'name'         => 'Starter',
                'price'        => 54999,
                'quota_gb'     => 15,
                'bucket_limit' => 10,
            ],
            'pro' => [
                'name'         => 'Pro',
                'price'        => 119999,
                'quota_gb'     => 30,
                'bucket_limit' => 50,
            ],
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function storageBuckets()
    {
        return $this->hasMany(StorageBucket::class, 'user_id', 'user_id');
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class, 'subscription_id');
    }
}

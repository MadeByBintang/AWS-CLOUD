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
        'access_key_limit',
        'price',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    /**
     * Map internal plan keys → nama tampilan yang benar untuk user.
     */
    public static function planLabels(): array
    {
        return [
            'free'    => 'Free',
            'starter' => 'Pro',
            'pro'     => 'Business',
        ];
    }

    /**
     * Nama tampilan paket ini (Pro, Business, dll).
     */
    public function displayName(): string
    {
        return static::planLabels()[$this->plan] ?? ucfirst($this->plan);
    }

    /**
     * Daftar paket storage yang tersedia.
     */
    public static function availablePlans(): array
    {
        return [
            'free' => [
                'name'              => 'Free',
                'price'             => 0,
                'quota_gb'          => 5,
                'bucket_limit'      => 3,
                'access_key_limit'  => 2,
            ],
            'starter' => [
                'name'              => 'Pro',        // ditampilkan sebagai "Pro"
                'price'             => 54999,
                'quota_gb'          => 15,
                'bucket_limit'      => 20,
                'access_key_limit'  => 10,
            ],
            'pro' => [
                'name'              => 'Business',   // ditampilkan sebagai "Business"
                'price'             => 119999,
                'quota_gb'          => 30,
                'bucket_limit'      => 99999,        // Unlimited
                'access_key_limit'  => 50,
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

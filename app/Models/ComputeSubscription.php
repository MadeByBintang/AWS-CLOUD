<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputeSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'compute_units',
        'vcpu_limit',
        'ram_go',
        'price',
        'is_active',
        'archive',
        'sequence_at',
    ];

    protected $casts = [
        'sequence_at' => 'datetime',
        'is_active'   => 'boolean',
        'archive'     => 'boolean',
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
     * Nama tampilan paket ini.
     */
    public function displayName(): string
    {
        return static::planLabels()[$this->plan] ?? ucfirst($this->plan);
    }

    /**
     * Daftar paket compute yang tersedia.
     */
    public static function availablePlans(): array
    {
        return [
            'free' => [
                'name'          => 'Free',
                'price'         => 0,
                'compute_units' => 10,
                'vcpu_limit'    => 1,
                'ram_go'        => 1,
            ],
            'starter' => [
                'name'          => 'Pro',
                'price'         => 54999, // bundled price (can match storage or be ignored if bundled)
                'compute_units' => 500,
                'vcpu_limit'    => 2,
                'ram_go'        => 8,
            ],
            'pro' => [
                'name'          => 'Business',
                'price'         => 119999,
                'compute_units' => 5000,
                'vcpu_limit'    => 8,
                'ram_go'        => 32,
            ],
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function computeInstances()
    {
        return $this->hasMany(ComputeInstance::class, 'subscription_id');
    }
}

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
     * Daftar paket compute yang tersedia.
     */
    public static function availablePlans(): array
    {
        return [
            'free' => [
                'name'          => 'Free',
                'price'         => 0,
                'compute_units' => 100,
                'vcpu_limit'    => 1,
                'ram_go'        => 1,
            ],
            'starter' => [
                'name'          => 'Starter',
                'price'         => 79000,
                'compute_units' => 500,
                'vcpu_limit'    => 2,
                'ram_go'        => 4,
            ],
            'pro' => [
                'name'          => 'Pro',
                'price'         => 199000,
                'compute_units' => 2000,
                'vcpu_limit'    => 8,
                'ram_go'        => 16,
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

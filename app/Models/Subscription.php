<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'bucket_limit',
        'key_limit',
        'storage_quota_gb',
        'compute_units',
        'is_active',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function availablePlans(): array
    {
        return [
            'free' => [
                'name'         => 'Free',
                'price'        => 0,
                'bucket_limit' => 3,
                'key_limit'    => 2,
                'storage_gb'   => 5,
            ],
            'starter' => [
                'name'         => 'Pro',
                'price'        => 49000,
                'bucket_limit' => 10,
                'key_limit'    => 5,
                'storage_gb'   => 50,
            ],
            'pro' => [
                'name'         => 'Business',
                'price'        => 149000,
                'bucket_limit' => 50,
                'key_limit'    => 20,
                'storage_gb'   => 500,
            ],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

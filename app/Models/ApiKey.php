<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'name',
        'key_prefix',
        'hashed_key',
        'permissions',
        'rate_limit_per_min',
        'request_count',
        'is_active',
        'expires_at',
        'last_used_at',
    ];

    protected $casts = [
        'permissions'  => 'array',
        'is_active'    => 'boolean',
        'expires_at'   => 'datetime',
        'last_used_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function storageSubscription()
    {
        return $this->belongsTo(StorageSubscription::class, 'subscription_id');
    }

    public function tags()
    {
        return $this->morphMany(ResourceTag::class, 'resource');
    }
}

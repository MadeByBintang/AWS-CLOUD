<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputeInstance extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'name',
        'instance_type',
        'vcpu',
        'ram_gb',
        'os_image',
        'ip_address',
        'status',
        'started_at',
        'stopped_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function computeSubscription()
    {
        return $this->belongsTo(ComputeSubscription::class, 'subscription_id');
    }

    public function tags()
    {
        return $this->morphMany(ResourceTag::class, 'resource');
    }
}

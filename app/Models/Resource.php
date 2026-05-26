<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'user_id',
        'resource_type',
        'resource_id',
        'name',
        'region',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi polymorphic: mengarah ke model aktual (StorageBucket, ComputeInstance, ApiKey)
     */
    public function resourceable()
    {
        return $this->morphTo(__FUNCTION__, 'resource_type', 'resource_id');
    }
}

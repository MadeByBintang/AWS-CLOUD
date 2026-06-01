<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageBucket extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'ministack_name',
        'region',
        'is_public',
        'versioning',
        'size_bytes',
        'object_count',
        'is_active',
    ];

    protected $casts = [
        'is_public'  => 'boolean',
        'versioning' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function getSizeHumanAttribute()
    {
        $bytes = $this->size_bytes ?? 0;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function storageObjects()
    {
        return $this->hasMany(StorageObject::class, 'bucket_id');
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class, 'subscription_id', 'user_id');
    }

    public function tags()
    {
        return $this->morphMany(ResourceTag::class, 'resource');
    }
}

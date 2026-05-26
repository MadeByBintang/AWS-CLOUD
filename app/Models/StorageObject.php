<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageObject extends Model
{
    protected $fillable = [
        'bucket_id',
        'user_id',
        'object_key',
        'original_name',
        'content_type',
        'size_bytes',
        'etag',
        'storage_class',
        'is_deleted',
        'uploaded_at',
    ];

    protected $casts = [
        'is_deleted'  => 'boolean',
        'uploaded_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function bucket()
    {
        return $this->belongsTo(StorageBucket::class, 'bucket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metadata()
    {
        return $this->hasMany(ObjectMetadata::class, 'object_id');
    }

    public function tags()
    {
        return $this->morphMany(ResourceTag::class, 'resource');
    }
}

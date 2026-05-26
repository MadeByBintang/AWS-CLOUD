<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectMetadata extends Model
{
    protected $table = 'object_metadata';

    protected $fillable = [
        'object_id',
        'meta_key',
        'meta_value',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function storageObject()
    {
        return $this->belongsTo(StorageObject::class, 'object_id');
    }
}

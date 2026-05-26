<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceTag extends Model
{
    protected $fillable = [
        'resource_type',
        'resource_id',
        'key',
        'value',
    ];

    /**
     * Relasi polymorphic: tag dapat menempel ke berbagai model
     * (StorageBucket, ComputeInstance, ApiKey, StorageObject, dll.)
     */
    public function resource()
    {
        return $this->morphTo();
    }
}

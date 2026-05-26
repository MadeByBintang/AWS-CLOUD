<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageBucket extends Model
{
    protected $fillable = ['user_id', 'name', 'ministack_bucket_name', 'size_bytes', 'is_active'];
}

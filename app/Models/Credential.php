<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $fillable = ['user_id', 'access_key', 'secret_key', 'is_active'];
}

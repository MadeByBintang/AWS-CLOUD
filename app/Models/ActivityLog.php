<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'resource_type',
        'resource_name',
        'device_type',
        'status',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function booted()
    {
        static::created(function ($activity) {
            if ($activity->user_id) {
                $user = \App\Models\User::find($activity->user_id);
                if ($user) {
                    $icon = '📋';
                    if ($activity->resource_type === 'Storage') $icon = '🪣';
                    elseif ($activity->resource_type === 'Compute') $icon = '⚙';
                    elseif ($activity->resource_type === 'Database') $icon = '🗄';
                    elseif ($activity->resource_type === 'Credential') $icon = '🔑';

                    $title = $activity->action;
                    $message = $activity->resource_name ?? 'Aktivitas sistem baru.';

                    $user->notify(new \App\Notifications\SystemNotification($title, $message, $icon));
                }
            }
        });
    }

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

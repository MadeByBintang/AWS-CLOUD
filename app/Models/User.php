<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Storage ───────────────────────────────────────────────────

    public function storageSubscriptions()
    {
        return $this->hasMany(StorageSubscription::class);
    }

    /**
     * Ambil langganan storage aktif; jika belum ada, otomatis buat paket free.
     */
    public function getOrCreateStorageSub(): StorageSubscription
    {
        $sub = $this->storageSubscriptions()->where('is_active', true)->latest()->first();

        if (! $sub) {
            $free = StorageSubscription::availablePlans()['free'];
            $sub  = StorageSubscription::create([
                'user_id'      => $this->id,
                'plan'         => 'free',
                'quota_gb'     => $free['quota_gb'],
                'bucket_limit' => $free['bucket_limit'],
                'price'        => 0,
                'is_active'    => true,
                'expires_at'   => null,
            ]);
        }

        return $sub;
    }

    public function storageBuckets()
    {
        return $this->hasMany(StorageBucket::class);
    }

    public function storageObjects()
    {
        return $this->hasMany(StorageObject::class);
    }

    public function getStorageUsedGbAttribute()
    {
        $bytes = $this->storageBuckets()->sum('size_bytes');
        return round($bytes / 1073741824, 2);
    }

    public function getStorageQuotaGbAttribute()
    {
        return $this->getOrCreateStorageSub()->quota_gb;
    }

    public function getStorageUsedPercentageAttribute()
    {
        $quota = $this->storage_quota_gb;
        if ($quota <= 0) return 0;
        return min(100, round(($this->storage_used_gb / $quota) * 100));
    }

    // ── Compute ───────────────────────────────────────────────────

    public function computeSubscriptions()
    {
        return $this->hasMany(ComputeSubscription::class);
    }

    /**
     * Ambil langganan compute aktif; jika belum ada, otomatis buat paket free.
     */
    public function getOrCreateComputeSub(): ComputeSubscription
    {
        $sub = $this->computeSubscriptions()->where('is_active', true)->latest()->first();

        if (! $sub) {
            $free = ComputeSubscription::availablePlans()['free'];
            $sub  = ComputeSubscription::create([
                'user_id'       => $this->id,
                'plan'          => 'free',
                'compute_units' => $free['compute_units'],
                'vcpu_limit'    => $free['vcpu_limit'],
                'ram_go'        => $free['ram_go'],
                'price'         => 0,
                'is_active'     => true,
                'archive'       => false,
                'sequence_at'   => now(),
            ]);
        }

        return $sub;
    }

    public function computeInstances()
    {
        return $this->hasMany(ComputeInstance::class);
    }

    // ── Credentials (S3 Access Keys) ──────────────────────────────

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }

    // ── API Keys ──────────────────────────────────────────────────

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    // ── Activity & Resources ──────────────────────────────────────

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
}

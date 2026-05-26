<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StorageSubscription;
use App\Models\ComputeSubscription;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user demo
        $user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Buat langganan storage free awal
        StorageSubscription::create([
            'user_id'      => $user->id,
            'plan'         => 'free',
            'quota_gb'     => 5,
            'bucket_limit' => 3,
            'price'        => 0,
            'is_active'    => true,
            'expires_at'   => null,
        ]);

        // Buat langganan compute free awal
        ComputeSubscription::create([
            'user_id'       => $user->id,
            'plan'          => 'free',
            'compute_units' => 100,
            'vcpu_limit'    => 1,
            'ram_go'        => 1,
            'price'         => 0,
            'is_active'     => true,
            'archive'       => false,
            'sequence_at'   => now(),
        ]);
    }
}

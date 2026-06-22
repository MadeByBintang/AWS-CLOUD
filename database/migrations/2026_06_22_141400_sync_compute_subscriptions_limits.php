<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Sinkronkan data yang sudah ada sesuai plan baru
        DB::table('compute_subscriptions')->where('plan', 'free')->update([
            'compute_units' => 10,
            'vcpu_limit'    => 1,
            'ram_go'        => 1,
        ]);

        DB::table('compute_subscriptions')->where('plan', 'starter')->update([
            'compute_units' => 500,
            'vcpu_limit'    => 2,
            'ram_go'        => 4,
        ]);

        DB::table('compute_subscriptions')->where('plan', 'pro')->update([
            'compute_units' => 5000,
            'vcpu_limit'    => 8,
            'ram_go'        => 16,
        ]);
    }

    public function down(): void
    {
        // No down migration needed
    }
};

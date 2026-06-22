<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('compute_subscriptions')->where('plan', 'starter')->update([
            'ram_go' => 8,
        ]);

        DB::table('compute_subscriptions')->where('plan', 'pro')->update([
            'ram_go' => 32,
        ]);
    }

    public function down(): void
    {
        // No down migration needed
    }
};

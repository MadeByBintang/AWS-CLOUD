<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('storage_quota_gb')->default(10)->after('key_limit');
            $table->integer('compute_units')->default(100)->after('storage_quota_gb');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['storage_quota_gb', 'compute_units', 'expires_at']);
        });
    }
};

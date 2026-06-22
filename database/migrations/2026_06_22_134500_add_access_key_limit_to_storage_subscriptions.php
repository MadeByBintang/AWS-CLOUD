<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom access_key_limit ke storage_subscriptions
        Schema::table('storage_subscriptions', function (Blueprint $table) {
            $table->integer('access_key_limit')->default(2)->after('bucket_limit');
        });

        // Sinkronkan data yang sudah ada sesuai plan
        DB::table('storage_subscriptions')->where('plan', 'free')->update([
            'quota_gb'         => 5,
            'bucket_limit'     => 3,
            'access_key_limit' => 2,
        ]);

        DB::table('storage_subscriptions')->where('plan', 'starter')->update([
            'quota_gb'         => 15,
            'bucket_limit'     => 20,
            'access_key_limit' => 10,
        ]);

        DB::table('storage_subscriptions')->where('plan', 'pro')->update([
            'quota_gb'         => 30,
            'bucket_limit'     => 99999, // Unlimited
            'access_key_limit' => 50,
        ]);
    }

    public function down(): void
    {
        Schema::table('storage_subscriptions', function (Blueprint $table) {
            $table->dropColumn('access_key_limit');
        });
    }
};

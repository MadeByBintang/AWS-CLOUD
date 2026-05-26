<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('storage_subscriptions')->cascadeOnDelete();
            $table->string('name');                                   // label API key
            $table->string('key_prefix', 8);                         // prefix tampilan (mis. "sk-abc12")
            $table->string('hashed_key');                             // hash key sebenarnya
            $table->json('permissions')->nullable();                  // daftar permission
            $table->integer('rate_limit_per_min')->default(60);      // batas request per menit
            $table->bigInteger('request_count')->default(0);         // total request yang sudah dibuat
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();              // waktu kedaluwarsa
            $table->timestamp('last_used_at')->nullable();            // waktu terakhir digunakan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};

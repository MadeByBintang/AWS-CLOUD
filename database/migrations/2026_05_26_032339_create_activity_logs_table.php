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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action');                       // nama aksi (mis. "Create Bucket")
            $table->string('resource_type');                // jenis resource (Storage, Compute, dll.)
            $table->string('resource_name');                // nama resource yang terdampak
            $table->string('device_type')->nullable();      // desktop, mobile, api
            $table->string('status')->nullable();           // success, failed, pending
            $table->string('ip_address')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

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
        Schema::create('compute_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('compute_subscriptions')->cascadeOnDelete();
            $table->string('name');                                  // nama instance
            $table->string('instance_type');                         // t2.micro, m5.large, dll.
            $table->integer('vcpu')->default(1);                     // jumlah vCPU
            $table->integer('ram_gb')->default(1);                   // RAM dalam GB
            $table->string('os_image')->nullable();                  // AMI / OS image
            $table->string('ip_address')->nullable();                // IP publik instance
            $table->string('status')->default('stopped');            // running, stopped, terminated
            $table->timestamp('started_at')->nullable();             // waktu terakhir start
            $table->timestamp('stopped_at')->nullable();             // waktu terakhir stop
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compute_instances');
    }
};

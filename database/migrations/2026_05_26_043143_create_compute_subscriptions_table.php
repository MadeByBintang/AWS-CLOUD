<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compute_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan')->default('free');               // free, starter, pro
            $table->integer('compute_units')->default(100);        // unit komputasi
            $table->integer('vcpu_limit')->default(1);             // maks vCPU
            $table->integer('ram_go')->default(1);                 // maks RAM (GB)
            $table->decimal('price', 10, 2)->default(0);          // harga per periode
            $table->boolean('is_active')->default(true);
            $table->boolean('archive')->default(false);            // arsip/dihentikan
            $table->timestamp('sequence_at')->nullable();          // tanggal aktivasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compute_subscriptions');
    }
};

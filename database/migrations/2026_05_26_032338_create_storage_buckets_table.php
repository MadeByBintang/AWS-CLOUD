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
        Schema::create('storage_buckets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');                                // nama tampilan bucket
            $table->string('ministack_name')->unique();           // nama unik di MiniStack
            $table->string('region')->nullable();                  // region deployment
            $table->boolean('is_public')->default(false);         // apakah bucket publik
            $table->boolean('versioning')->default(false);        // versioning aktif
            $table->unsignedBigInteger('size_bytes')->default(0); // total ukuran konten
            $table->integer('object_count')->default(0);          // total jumlah objek
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_buckets');
    }
};

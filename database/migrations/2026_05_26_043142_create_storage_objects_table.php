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
        Schema::create('storage_objects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bucket_id')->constrained('storage_buckets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('object_key');                          // path/key objek di bucket
            $table->string('original_name');                       // nama file asli
            $table->string('content_type')->nullable();            // MIME type
            $table->unsignedBigInteger('size_bytes')->default(0); // ukuran file
            $table->string('etag')->nullable();                    // hash untuk verifikasi
            $table->string('storage_class')->default('STANDARD'); // STANDARD, IA, GLACIER
            $table->boolean('is_deleted')->default(false);         // soft delete
            $table->timestamp('uploaded_at')->nullable();          // waktu upload
            $table->timestamps();

            $table->index(['bucket_id', 'object_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_objects');
    }
};

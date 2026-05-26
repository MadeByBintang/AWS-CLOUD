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
        Schema::create('object_metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('object_id')->constrained('storage_objects')->cascadeOnDelete();
            $table->string('meta_key');     // kunci metadata (e.g. "author", "x-amz-acl")
            $table->text('meta_value');     // nilai metadata
            $table->timestamps();

            $table->index(['object_id', 'meta_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('object_metadata');
    }
};

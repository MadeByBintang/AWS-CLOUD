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
        Schema::create('resource_tags', function (Blueprint $table) {
            $table->id();
            // Polymorphic: bisa menempel di storage_buckets, compute_instances, api_keys, dll.
            $table->string('resource_type'); // nama model/tabel terkait
            $table->unsignedBigInteger('resource_id'); // ID record terkait
            $table->string('key');           // kunci tag (mis. "Environment")
            $table->string('value');         // nilai tag (mis. "Production")
            $table->timestamps();

            $table->index(['resource_type', 'resource_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_tags');
    }
};

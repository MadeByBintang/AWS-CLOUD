<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan')->default('free');               // free, starter, pro
            $table->double('quota_gb')->default(5);               // kuota storage GB
            $table->integer('bucket_limit')->default(3);          // maks jumlah bucket
            $table->decimal('price', 10, 2)->default(0);          // harga per periode
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_subscriptions');
    }
};

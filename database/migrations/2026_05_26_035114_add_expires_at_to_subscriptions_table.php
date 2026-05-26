<?php

use Illuminate\Database\Migrations\Migration;

// Kolom expires_at sudah dimasukkan langsung ke storage_subscriptions & compute_subscriptions (ERD v2)
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};

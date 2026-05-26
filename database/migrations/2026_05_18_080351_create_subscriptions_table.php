<?php

use Illuminate\Database\Migrations\Migration;

// Tabel subscriptions lama digantikan oleh storage_subscriptions dan compute_subscriptions (ERD v2)
// Migration ini dikosongkan untuk mempertahankan urutan file
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};

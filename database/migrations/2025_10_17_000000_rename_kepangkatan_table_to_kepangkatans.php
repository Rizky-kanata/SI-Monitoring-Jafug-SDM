<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('kepangkatan') && ! Schema::hasTable('kepangkatans')) {
            Schema::rename('kepangkatan', 'kepangkatans');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('kepangkatans') && ! Schema::hasTable('kepangkatan')) {
            Schema::rename('kepangkatans', 'kepangkatan');
        }
    }
};

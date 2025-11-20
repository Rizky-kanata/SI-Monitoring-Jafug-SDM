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
        if (Schema::hasColumn('profils', 'kelompok_keahlian')) {
            Schema::table('profils', function (Blueprint $table) {
                $table->dropColumn('kelompok_keahlian');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('profils', 'kelompok_keahlian')) {
            Schema::table('profils', function (Blueprint $table) {
                $table->string('kelompok_keahlian')->default('RIIB');
            });
        }
    }
};

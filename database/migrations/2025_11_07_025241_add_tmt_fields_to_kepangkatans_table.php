<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kepangkatans', function (Blueprint $table) {
            $table->date('tanggal_tmt')->nullable()->after('tanggal_mulai');
            $table->boolean('is_published')->default(false)->after('status');
        });

        DB::table('kepangkatans')
            ->whereNull('tanggal_tmt')
            ->update([
                'tanggal_tmt' => DB::raw('tanggal_mulai'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kepangkatans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_tmt', 'is_published']);
        });
    }
};

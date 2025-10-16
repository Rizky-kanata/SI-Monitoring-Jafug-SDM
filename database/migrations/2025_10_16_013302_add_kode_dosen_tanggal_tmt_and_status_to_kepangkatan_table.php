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
        Schema::table('kepangkatan', function (Blueprint $table) {
            $table->string('kode_dosen')->nullable()->after('nama_dosen');
            $table->date('tanggal_tmt')->nullable()->after('jabatan_fungsional');
            $table->enum('status_publikasi', [
                'belum_diajukan',
                'menunggu_verifikasi',
                'perlu_revisi',
                'siap_publikasi',
                'terpublikasi',
            ])->default('belum_diajukan')->after('tanggal_tmt');

            $table->unique('kode_dosen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kepangkatan', function (Blueprint $table) {
            $table->dropUnique(['kode_dosen']);
            $table->dropColumn(['kode_dosen', 'tanggal_tmt', 'status_publikasi']);
        });
    }
};

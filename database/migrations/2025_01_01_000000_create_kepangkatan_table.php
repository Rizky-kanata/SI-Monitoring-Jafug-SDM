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
        Schema::create('kepangkatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profil_id')
                ->unique()
                ->constrained('profils')
                ->cascadeOnDelete();
            $table->string('jabatan_fungsional');
            $table->string('pangkat')->nullable();
            $table->string('golongan')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepangkatans');
    }
};

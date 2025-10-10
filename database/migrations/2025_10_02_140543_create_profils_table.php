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
        Schema::create('profils', function (Blueprint $table) {
            $table->id();
            $table->string('kode_dosen')->unique();
            $table->string('nama_dosen');
            $table->string('prodi');
            $table->string('kelompok_keahlian');
            $table->string('sub_kelompok_keahlian');
            $table->string('nip')->nullable()->unique();
            $table->string('nidn')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profils');
    }
};

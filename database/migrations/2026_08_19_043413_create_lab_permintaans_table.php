<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_permintaan', function (Blueprint $table) {
            $table->id();

            $table->string('no_lab')->unique();

            $table->foreignId('pendaftaran_id')
                ->constrained('pendaftarans')
                ->cascadeOnDelete();

            $table->foreignId('dokter_id')
                ->constrained('dokters')
                ->restrictOnDelete();

            $table->date('tanggal_permintaan');

            $table->enum('status', [
                'MENUNGGU',
                'SAMPEL_DIAMBIL',
                'DIPROSES',
                'SELESAI',
                'DIVERIFIKASI',
                'BATAL',
            ])->default('MENUNGGU');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_permintaan');
    }
};
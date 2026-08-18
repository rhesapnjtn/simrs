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
    Schema::create('pendaftarans', function (Blueprint $table) {

        $table->id();

        $table->foreignId('pasien_id')
            ->constrained('pasiens')
            ->cascadeOnDelete();

        $table->foreignId('poli_id')
            ->constrained('polis')
            ->cascadeOnDelete();

        $table->foreignId('dokter_id')
            ->constrained('dokters')
            ->cascadeOnDelete();

        $table->date('tanggal_kunjungan');

        $table->string('no_antrian', 20);

        $table->enum('status', [
            'MENUNGGU',
            'DIPANGGIL',
            'DIPERIKSA',
            'SELESAI',
            'BATAL'
        ])->default('MENUNGGU');

        $table->text('keluhan')->nullable();

        $table->timestamps();

        $table->unique([
            'poli_id',
            'tanggal_kunjungan',
            'no_antrian'
        ]);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('pendaftarans');
}
};

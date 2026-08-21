<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_hasil', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lab_permintaan_detail_id')
                ->constrained('lab_permintaan_details')
                ->cascadeOnDelete();

            $table->text('hasil')->nullable();

            $table->enum('flag', [
                'NORMAL',
                'TINGGI',
                'RENDAH',
                'KRITIS',
            ])->nullable();

            $table->text('catatan')->nullable();

            $table->dateTime('tanggal_pemeriksaan')->nullable();

            $table->dateTime('tanggal_verifikasi')->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_hasil');
    }
};
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
        Schema::create('pemeriksaans', function (Blueprint $table) {

            $table->id();

            $table->foreignId('pendaftaran_id')
                ->unique()
                ->constrained('pendaftarans')
                ->cascadeOnDelete();

            // Tanda vital
            $table->string('tekanan_darah', 20)->nullable();
            $table->decimal('suhu', 4, 1)->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->integer('nadi')->nullable();
            $table->integer('respirasi')->nullable();

            // Hasil pemeriksaan
            $table->text('diagnosis')->nullable();
            $table->text('tindakan')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaans');
    }
};
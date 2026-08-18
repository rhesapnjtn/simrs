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
        Schema::create('resep_details', function (Blueprint $table) {
    $table->id();

    $table->foreignId('resep_id')
        ->constrained('reseps')
        ->cascadeOnDelete();

    $table->foreignId('obat_id')
        ->constrained('obats')
        ->restrictOnDelete();

    $table->integer('jumlah');

    $table->string('dosis');
    $table->string('aturan_pakai')->nullable();

    $table->text('catatan')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_details');
    }
};

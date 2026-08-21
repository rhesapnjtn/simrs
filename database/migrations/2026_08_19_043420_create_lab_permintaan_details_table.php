<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_permintaan_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lab_permintaan_id')
                ->constrained('lab_permintaan')
                ->cascadeOnDelete();

            $table->foreignId('lab_pemeriksaan_id')
                ->constrained('lab_pemeriksaans')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                [
                    'lab_permintaan_id',
                    'lab_pemeriksaan_id',
                ],
                'lab_permintaan_detail_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_permintaan_details');
    }
};
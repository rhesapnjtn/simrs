<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            $table->foreignId('dokter_id')
                ->nullable()
                ->after('pendaftaran_id')
                ->constrained('dokters')
                ->nullOnDelete();

            $table->date('tanggal_resep')
                ->nullable()
                ->after('dokter_id');
        });
    }

    public function down(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            $table->dropForeign(['dokter_id']);
            $table->dropColumn(['dokter_id', 'tanggal_resep']);
        });
    }
};
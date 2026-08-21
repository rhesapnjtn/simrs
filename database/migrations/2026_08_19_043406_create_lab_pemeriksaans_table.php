<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_pemeriksaans', function (Blueprint $table) {
            $table->id();

            $table->string('kode')->unique();

            $table->string('nama');

            $table->string('kategori')->nullable();

            $table->string('satuan')->nullable();

            $table->string('nilai_rujukan')->nullable();

            $table->decimal('harga', 15, 2)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_pemeriksaans');
    }
};
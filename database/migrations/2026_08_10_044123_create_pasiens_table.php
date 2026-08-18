<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasiens', function (Blueprint $table) {

            $table->id();

            $table->string('no_rm', 30)->unique();

            $table->string('nik', 20)
                ->nullable()
                ->unique();

            $table->string('nama');

            $table->enum('jenis_kelamin', [
                'L',
                'P'
            ]);

            $table->date('tanggal_lahir');

            $table->text('alamat')->nullable();

            $table->string('no_telepon', 30)->nullable();

            $table->string('golongan_darah', 5)
                ->nullable();

            $table->string('kontak_darurat', 100)
                ->nullable();

            $table->string('no_telepon_darurat', 30)
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};
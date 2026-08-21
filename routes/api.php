<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PoliController;
use App\Http\Controllers\Api\DokterController;
use App\Http\Controllers\Api\PasienController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\Api\ApotekController;
use App\Http\Controllers\Api\LabPemeriksaanController;
use App\Http\Controllers\Api\LabPermintaanController;
use App\Http\Controllers\Api\LabHasilPdfController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\ResepController;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
|
| Semua route di file ini menggunakan prefix /api
|
*/


Route::middleware('web')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/login',
        [AuthController::class, 'login']
    );


    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED USER
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {


        /*
        |--------------------------------------------------------------------------
        | USER LOGIN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/user',
            [AuthController::class, 'user']
        );

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        );


        /*
        |--------------------------------------------------------------------------
        | LABORATORIUM - MASTER PEMERIKSAAN AKTIF
        |--------------------------------------------------------------------------
        |
        | PENTING:
        | Route /active harus berada sebelum:
        |
        | /lab-pemeriksaans/{labPemeriksaan}
        |
        | agar "active" tidak dianggap sebagai ID pemeriksaan.
        |
        | Bisa digunakan oleh:
        | - SUPER_ADMIN
        | - DOKTER
        | - PETUGAS LAB
        |
        */

        Route::get(
            '/lab-pemeriksaans/active',
            [LabPemeriksaanController::class, 'active']
        );
    

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        Route::middleware('superadmin')->group(function () {


            /*
            |--------------------------------------------------------------------------
            | USER MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/users',
                [UserController::class, 'index']
            );

            Route::post(
                '/users',
                [UserController::class, 'store']
            );

            Route::get(
                '/users/{user}',
                [UserController::class, 'show']
            );

            Route::put(
                '/users/{user}',
                [UserController::class, 'update']
            );

            Route::delete(
                '/users/{user}',
                [UserController::class, 'destroy']
            );

            Route::get(
                '/roles',
                [UserController::class, 'roles']
            );


            /*
            |--------------------------------------------------------------------------
            | PENDAFTARAN MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/pendaftaran-form',
                [PendaftaranController::class, 'form']
            );

            Route::post(
                '/pendaftarans',
                [PendaftaranController::class, 'store']
            );

            Route::get(
                '/pendaftarans/{pendaftaran}',
                [PendaftaranController::class, 'show']
            );

            Route::put(
                '/pendaftarans/{pendaftaran}/status',
                [PendaftaranController::class, 'updateStatus']
            );


            /*
            |--------------------------------------------------------------------------
            | POLI MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/polis',
                [PoliController::class, 'index']
            );

            Route::post(
                '/polis',
                [PoliController::class, 'store']
            );

            Route::get(
                '/polis/{poli}',
                [PoliController::class, 'show']
            );

            Route::put(
                '/polis/{poli}',
                [PoliController::class, 'update']
            );

            Route::delete(
                '/polis/{poli}',
                [PoliController::class, 'destroy']
            );


            /*
            |--------------------------------------------------------------------------
            | DOKTER MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/dokters',
                [DokterController::class, 'index']
            );

            Route::post(
                '/dokters',
                [DokterController::class, 'store']
            );

            Route::get(
                '/dokters/{dokter}',
                [DokterController::class, 'show']
            );

            Route::put(
                '/dokters/{dokter}',
                [DokterController::class, 'update']
            );

            Route::delete(
                '/dokters/{dokter}',
                [DokterController::class, 'destroy']
            );

            Route::get(
                '/dokter-users',
                [DokterController::class, 'users']
            );

            Route::get(
                '/dokter-polis',
                [DokterController::class, 'polis']
            );


            /*
            |--------------------------------------------------------------------------
            | MASTER PEMERIKSAAN LABORATORIUM
            |--------------------------------------------------------------------------
            |
            | CRUD hanya untuk SUPER_ADMIN.
            |
            */

            Route::get(
                '/lab-pemeriksaans',
                [LabPemeriksaanController::class, 'index']
            );

            Route::post(
                '/lab-pemeriksaans',
                [LabPemeriksaanController::class, 'store']
            );

            Route::get(
                '/lab-pemeriksaans/{labPemeriksaan}',
                [LabPemeriksaanController::class, 'show']
            );

            Route::put(
                '/lab-pemeriksaans/{labPemeriksaan}',
                [LabPemeriksaanController::class, 'update']
            );

            Route::delete(
                '/lab-pemeriksaans/{labPemeriksaan}',
                [LabPemeriksaanController::class, 'destroy']
            );
        });


        /*
        |--------------------------------------------------------------------------
        | PENDAFTARAN - READ ACCESS
        |--------------------------------------------------------------------------
        |
        | Digunakan oleh:
        | - SUPER_ADMIN
        | - DOKTER
        | - PERAWAT
        |
        */

        Route::get(
            '/pendaftarans',
            [PendaftaranController::class, 'index']
        );


        /*
        |--------------------------------------------------------------------------
        | MASTER PASIEN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/pasiens',
            [PasienController::class, 'index']
        );

        Route::post(
            '/pasiens',
            [PasienController::class, 'store']
        );

        Route::get(
            '/pasiens/{pasien}',
            [PasienController::class, 'show']
        );

        Route::put(
            '/pasiens/{pasien}',
            [PasienController::class, 'update']
        );

        Route::delete(
            '/pasiens/{pasien}',
            [PasienController::class, 'destroy']
        );


        /*
        |--------------------------------------------------------------------------
        | DOKTER - READ ACCESS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dokters',
            [DokterController::class, 'index']
        );


        /*
        |--------------------------------------------------------------------------
        | PEMERIKSAAN PASIEN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/pendaftarans/{pendaftaran}/pemeriksaan',
            [PemeriksaanController::class, 'show']
        );

        Route::post(
            '/pendaftarans/{pendaftaran}/pemeriksaan',
            [PemeriksaanController::class, 'store']
        );


        /*
        |--------------------------------------------------------------------------
        | RIWAYAT PEMERIKSAAN PASIEN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/pasien/{pasien}/riwayat-pemeriksaan',
            [PemeriksaanController::class, 'riwayatPasien']
        );

        Route::get(
            '/dokter/pasien-riwayat',
            [PemeriksaanController::class, 'pasienDokter']
        );


        /*
        |--------------------------------------------------------------------------
        | OBAT
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/obats',
            [ObatController::class, 'index']
        );

        Route::post(
            '/obats',
            [ObatController::class, 'store']
        );

        Route::get(
            '/obats/{obat}',
            [ObatController::class, 'show']
        );

        Route::put(
            '/obats/{obat}',
            [ObatController::class, 'update']
        );

        Route::delete(
            '/obats/{obat}',
            [ObatController::class, 'destroy']
        );


        /*
        |--------------------------------------------------------------------------
        | RESEP
        |--------------------------------------------------------------------------
        |
        | Digunakan oleh:
        | - SUPER_ADMIN
        | - DOKTER
        |
        */

        Route::get(
            '/reseps',
            [ResepController::class, 'index']
        );

        Route::post(
            '/reseps',
            [ResepController::class, 'store']
        );

        Route::get(
            '/reseps/{id}',
            [ResepController::class, 'show']
        );

        Route::put(
            '/reseps/{id}/status',
            [ResepController::class, 'updateStatus']
        );

        Route::delete(
            '/reseps/{id}',
            [ResepController::class, 'destroy']
        );


        /*
        |--------------------------------------------------------------------------
        | APOTEK
        |--------------------------------------------------------------------------
        |
        | Digunakan oleh bagian farmasi.
        |
        */

        Route::get(
            '/apotek/reseps',
            [ApotekController::class, 'index']
        );

        Route::get(
            '/apotek/reseps/{id}',
            [ApotekController::class, 'show']
        );

        Route::put(
            '/apotek/reseps/{id}/proses',
            [ApotekController::class, 'proses']
        );

        Route::put(
            '/apotek/reseps/{id}/selesai',
            [ApotekController::class, 'selesai']
        );


        /*
        |--------------------------------------------------------------------------
        | LABORATORIUM - PERMINTAAN
        |--------------------------------------------------------------------------
        |
        | Dokter dapat membuat permintaan laboratorium.
        |
        */

        Route::get(
            '/lab-permintaans',
            [LabPermintaanController::class, 'index']
        );

        Route::post(
            '/lab-permintaans',
            [LabPermintaanController::class, 'store']
        );

        Route::get(
            '/lab-permintaans/{labPermintaan}',
            [LabPermintaanController::class, 'show']
        );

        Route::put(
            '/lab-permintaans/{labPermintaan}',
            [LabPermintaanController::class, 'update']
        );

        Route::put(
            '/lab-permintaans/{labPermintaan}/status',
            [LabPermintaanController::class, 'updateStatus']
        );

        Route::delete(
            '/lab-permintaans/{labPermintaan}',
            [LabPermintaanController::class, 'destroy']
        );


        /*
        |--------------------------------------------------------------------------
        | LABORATORIUM - HASIL
        |--------------------------------------------------------------------------
        |
        | Petugas laboratorium dapat memasukkan hasil.
        |
        */

        Route::post(
            '/lab-permintaan-details/{detail}/hasil',
            [LabPermintaanController::class, 'storeHasil']
        );

        Route::put(
            '/lab-hasil/{hasil}',
            [LabPermintaanController::class, 'updateHasil']
        );

        Route::put(
            '/lab-hasil/{hasil}/verifikasi',
            [LabPermintaanController::class, 'verifikasiHasil']
        );
        /*
|--------------------------------------------------------------------------
| LABORATORIUM - CETAK HASIL
|--------------------------------------------------------------------------
*/

Route::get(
    '/lab-permintaans/{labPermintaan}/cetak',
    [LabHasilPdfController::class, 'cetak']
);
    });
});
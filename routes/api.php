<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PoliController;
use App\Http\Controllers\Api\DokterController;
use App\Http\Controllers\Api\PasienController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\Api\ApotekController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
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
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        |
        | Semua endpoint di dalam group ini hanya dapat
        | digunakan oleh SUPER_ADMIN.
        |
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
            |
            | Pembuatan dan perubahan status pendaftaran
            | tetap menjadi hak SUPER_ADMIN.
            |
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

        });


        /*
        |--------------------------------------------------------------------------
        | PENDAFTARAN - READ ACCESS
        |--------------------------------------------------------------------------
        |
        | Endpoint ini dibutuhkan oleh:
        |
        | - Dokter
        | - Perawat
        | - Super Admin
        |
        | Dokter membutuhkan data pendaftaran untuk:
        | - Pemeriksaan
        | - Resep
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
        |
        | Digunakan oleh:
        | - SUPER_ADMIN
        | - ADMIN
        | - DOKTER
        | - PERAWAT
        |
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
        |
        | Endpoint GET /dokters dibutuhkan oleh dokter
        | ketika membuat resep.
        |
        | Contoh:
        |
        | ResepPage.vue
        | axios.get('/api/dokters')
        |
        */

        Route::get(
            '/dokters',
            [DokterController::class, 'index']
        );


        /*
        |--------------------------------------------------------------------------
        | PEMERIKSAAN PASIEN
        |--------------------------------------------------------------------------
        |
        | Bisa digunakan oleh:
        | - SUPER_ADMIN
        | - DOKTER
        | - PERAWAT
        |
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
Route::get(
    '/dokter/pasien-riwayat',
    [PemeriksaanController::class, 'pasienDokter']
);


        /*
        |--------------------------------------------------------------------------
        | OBAT
        |--------------------------------------------------------------------------
        |
        | Untuk sekarang tetap berada pada authenticated route.
        |
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

        Route::get(
            '/reseps/{id}',
            [ResepController::class, 'show']
        );

        Route::post(
            '/reseps',
            [ResepController::class, 'store']
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

    });
    

});
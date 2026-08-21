<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabPermintaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Throwable;

class LabHasilPdfController extends Controller
{
    /**
     * Cetak hasil pemeriksaan laboratorium
     */
    public function cetak(LabPermintaan $labPermintaan)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Load seluruh relasi yang dibutuhkan PDF
            |--------------------------------------------------------------------------
            */

            $labPermintaan->load([
                'pendaftaran.pasien',
                'pendaftaran.poli',
                'pendaftaran.dokter',

                'dokter',

                'details.labPemeriksaan',

                'details.hasil.verifiedBy',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Validasi data
            |--------------------------------------------------------------------------
            */

            if (!$labPermintaan->pendaftaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pendaftaran tidak ditemukan.',
                ], 404);
            }

            if (!$labPermintaan->pendaftaran->pasien) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pasien tidak ditemukan.',
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Pastikan ada detail pemeriksaan
            |--------------------------------------------------------------------------
            */

            if ($labPermintaan->details->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum terdapat pemeriksaan laboratorium.',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Pastikan semua pemeriksaan memiliki hasil
            |--------------------------------------------------------------------------
            */

            $belumAdaHasil = $labPermintaan->details
                ->filter(function ($detail) {
                    return !$detail->hasil;
                });

            if ($belumAdaHasil->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum semua pemeriksaan laboratorium memiliki hasil.',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Pastikan hasil sudah diverifikasi
            |--------------------------------------------------------------------------
            */

            $belumDiverifikasi = $labPermintaan->details
                ->filter(function ($detail) {
                    return !$detail->hasil ||
                        !$detail->hasil->tanggal_verifikasi;
                });

            if ($belumDiverifikasi->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil laboratorium belum diverifikasi seluruhnya.',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Generate PDF
            |--------------------------------------------------------------------------
            */

            $pdf = Pdf::loadView(
                'laboratorium.hasil-pdf',
                [
                    'labPermintaan' => $labPermintaan,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Ukuran dan orientasi kertas
            |--------------------------------------------------------------------------
            */

            $pdf->setPaper('A4', 'portrait');

            /*
            |--------------------------------------------------------------------------
            | Nama file
            |--------------------------------------------------------------------------
            */

            $fileName =
                'Hasil-Laboratorium-' .
                $labPermintaan->no_lab .
                '.pdf';

            /*
            |--------------------------------------------------------------------------
            | Return PDF
            |--------------------------------------------------------------------------
            */

            return $pdf->stream($fileName);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF hasil laboratorium.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LabPemeriksaan;

class LabPemeriksaanSeeder extends Seeder
{
    public function run(): void
    {
        $pemeriksaans = [

            // =====================================================
            // HEMATOLOGI
            // =====================================================

            [
                'kode' => 'LAB-001',
                'nama' => 'Hemoglobin',
                'kategori' => 'Hematologi',
                'satuan' => 'g/dL',
                'nilai_rujukan' => 'Pria: 13-17 | Wanita: 12-16',
                'harga' => 50000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-002',
                'nama' => 'Leukosit',
                'kategori' => 'Hematologi',
                'satuan' => '/µL',
                'nilai_rujukan' => '4.000-10.000',
                'harga' => 45000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-003',
                'nama' => 'Trombosit',
                'kategori' => 'Hematologi',
                'satuan' => '/µL',
                'nilai_rujukan' => '150.000-450.000',
                'harga' => 45000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-004',
                'nama' => 'Hematokrit',
                'kategori' => 'Hematologi',
                'satuan' => '%',
                'nilai_rujukan' => 'Pria: 40-54 | Wanita: 36-46',
                'harga' => 40000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-005',
                'nama' => 'Eritrosit',
                'kategori' => 'Hematologi',
                'satuan' => 'juta/µL',
                'nilai_rujukan' => 'Pria: 4,5-5,9 | Wanita: 4,1-5,1',
                'harga' => 40000,
                'is_active' => true,
            ],

            // =====================================================
            // KIMIA KLINIK
            // =====================================================

            [
                'kode' => 'LAB-006',
                'nama' => 'Gula Darah Sewaktu',
                'kategori' => 'Kimia Klinik',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => '< 200',
                'harga' => 35000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-007',
                'nama' => 'Gula Darah Puasa',
                'kategori' => 'Kimia Klinik',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => '70-100',
                'harga' => 40000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-008',
                'nama' => 'Gula Darah 2 Jam PP',
                'kategori' => 'Kimia Klinik',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => '< 140',
                'harga' => 40000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-009',
                'nama' => 'Kolesterol Total',
                'kategori' => 'Kimia Klinik',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => '< 200',
                'harga' => 50000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-010',
                'nama' => 'Asam Urat',
                'kategori' => 'Kimia Klinik',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => 'Pria: 3,4-7,0 | Wanita: 2,4-6,0',
                'harga' => 40000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-011',
                'nama' => 'Trigliserida',
                'kategori' => 'Kimia Klinik',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => '< 150',
                'harga' => 50000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-012',
                'nama' => 'HDL Kolesterol',
                'kategori' => 'Kimia Klinik',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => '> 40',
                'harga' => 50000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-013',
                'nama' => 'LDL Kolesterol',
                'kategori' => 'Kimia Klinik',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => '< 100',
                'harga' => 50000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-014',
                'nama' => 'Kreatinin',
                'kategori' => 'Kimia Klinik',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => '0,6-1,3',
                'harga' => 45000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-015',
                'nama' => 'Ureum',
                'kategori' => 'Kimia Klinik',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => '15-45',
                'harga' => 45000,
                'is_active' => true,
            ],

            // =====================================================
            // FUNGSI HATI
            // =====================================================

            [
                'kode' => 'LAB-016',
                'nama' => 'SGOT (AST)',
                'kategori' => 'Fungsi Hati',
                'satuan' => 'U/L',
                'nilai_rujukan' => '5-40',
                'harga' => 50000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-017',
                'nama' => 'SGPT (ALT)',
                'kategori' => 'Fungsi Hati',
                'satuan' => 'U/L',
                'nilai_rujukan' => '7-56',
                'harga' => 50000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-018',
                'nama' => 'Bilirubin Total',
                'kategori' => 'Fungsi Hati',
                'satuan' => 'mg/dL',
                'nilai_rujukan' => '0,3-1,2',
                'harga' => 50000,
                'is_active' => true,
            ],

            // =====================================================
            // FUNGSI GINJAL
            // =====================================================

            [
                'kode' => 'LAB-019',
                'nama' => 'eGFR',
                'kategori' => 'Fungsi Ginjal',
                'satuan' => 'mL/min/1,73m²',
                'nilai_rujukan' => '≥ 90',
                'harga' => 60000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-020',
                'nama' => 'Protein Urine',
                'kategori' => 'Fungsi Ginjal',
                'satuan' => '-',
                'nilai_rujukan' => 'Negatif',
                'harga' => 35000,
                'is_active' => true,
            ],

            // =====================================================
            // URINALISIS
            // =====================================================

            [
                'kode' => 'LAB-021',
                'nama' => 'Urinalisis Lengkap',
                'kategori' => 'Urinalisis',
                'satuan' => '-',
                'nilai_rujukan' => 'Normal',
                'harga' => 60000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-022',
                'nama' => 'pH Urine',
                'kategori' => 'Urinalisis',
                'satuan' => 'pH',
                'nilai_rujukan' => '4,5-8,0',
                'harga' => 30000,
                'is_active' => true,
            ],

            // =====================================================
            // IMUNOLOGI
            // =====================================================

            [
                'kode' => 'LAB-023',
                'nama' => 'HBsAg',
                'kategori' => 'Imunologi',
                'satuan' => '-',
                'nilai_rujukan' => 'Non-Reaktif',
                'harga' => 75000,
                'is_active' => true,
            ],

            [
                'kode' => 'LAB-024',
                'nama' => 'Widal',
                'kategori' => 'Imunologi',
                'satuan' => '-',
                'nilai_rujukan' => 'Negatif',
                'harga' => 60000,
                'is_active' => true,
            ],

            // =====================================================
            // PEMERIKSAAN LAIN
            // =====================================================

            [
                'kode' => 'LAB-025',
                'nama' => 'Golongan Darah',
                'kategori' => 'Hematologi',
                'satuan' => '-',
                'nilai_rujukan' => 'A/B/AB/O',
                'harga' => 30000,
                'is_active' => true,
            ],
        ];

        foreach ($pemeriksaans as $pemeriksaan) {
            LabPemeriksaan::updateOrCreate(
                [
                    'kode' => $pemeriksaan['kode'],
                ],
                $pemeriksaan
            );
        }
    }
}
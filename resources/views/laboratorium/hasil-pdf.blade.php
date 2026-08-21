<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Hasil Laboratorium - {{ $labPermintaan->no_lab }}
    </title>

    <style>

        @page {
            margin: 25px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .header h2 {
            margin: 4px 0;
            font-size: 13px;
            font-weight: normal;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 10px;
        }

        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 18px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 4px;
            vertical-align: top;
        }

        .info-label {
            width: 17%;
            font-weight: bold;
        }

        .info-separator {
            width: 2%;
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .result-table th {
            background: #eeeeee;
            border: 1px solid #555;
            padding: 7px 5px;
            text-align: center;
            font-size: 9px;
        }

        .result-table td {
            border: 1px solid #777;
            padding: 6px 5px;
            font-size: 9px;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        .status-box {
            margin-top: 18px;
            border: 1px solid #555;
            padding: 8px;
        }

        .status-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .notes {
            margin-top: 18px;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-container {
            width: 100%;
            margin-top: 45px;
        }

        .signature {
            width: 35%;
            margin-left: auto;
            text-align: center;
        }

        .signature-space {
            height: 55px;
        }

        .footer {
            position: fixed;
            bottom: -5px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

    </style>

</head>

<body>

    {{-- HEADER --}}

    <div class="header">

        <h1>
            SIMRS
        </h1>

        <h2>
            SISTEM INFORMASI MANAJEMEN RUMAH SAKIT
        </h2>

        <p>
            HASIL PEMERIKSAAN LABORATORIUM
        </p>

    </div>


    {{-- TITLE --}}

    <div class="title">
        HASIL PEMERIKSAAN LABORATORIUM
    </div>


    {{-- INFORMASI PASIEN --}}

    <table class="info-table">

        <tr>

            <td class="info-label">
                No. Lab
            </td>

            <td class="info-separator">
                :
            </td>

            <td>
                {{ $labPermintaan->no_lab ?? '-' }}
            </td>

            <td class="info-label">
                Tanggal
            </td>

            <td class="info-separator">
                :
            </td>

            <td>
                @if ($labPermintaan->tanggal_permintaan)
                    {{ $labPermintaan->tanggal_permintaan->format('d/m/Y') }}
                @else
                    -
                @endif
            </td>

        </tr>


        <tr>

            <td class="info-label">
                No. RM
            </td>

            <td class="info-separator">
                :
            </td>

            <td>
                {{ $labPermintaan->pendaftaran->pasien->no_rm ?? '-' }}
            </td>

            <td class="info-label">
                Dokter
            </td>

            <td class="info-separator">
                :
            </td>

            <td>
                {{ $labPermintaan->dokter->nama ?? '-' }}
            </td>

        </tr>


        <tr>

            <td class="info-label">
                Nama Pasien
            </td>

            <td class="info-separator">
                :
            </td>

            <td>
                {{ $labPermintaan->pendaftaran->pasien->nama ?? '-' }}
            </td>

            <td class="info-label">
                Poli
            </td>

            <td class="info-separator">
                :
            </td>

            <td>
                {{ $labPermintaan->pendaftaran->poli->nama ?? '-' }}
            </td>

        </tr>

    </table>


    {{-- HASIL PEMERIKSAAN --}}

    <table class="result-table">

        <thead>

            <tr>

                <th width="5%">
                    No
                </th>

                <th width="25%">
                    Pemeriksaan
                </th>

                <th width="15%">
                    Hasil
                </th>

                <th width="10%">
                    Satuan
                </th>

                <th width="20%">
                    Nilai Rujukan
                </th>

                <th width="10%">
                    Flag
                </th>

                <th width="15%">
                    Catatan
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse ($labPermintaan->details as $index => $detail)

                @php

                    $pemeriksaan = $detail->labPemeriksaan;
                    $hasil = $detail->hasil;

                @endphp

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>

                        <strong>
                            {{ $pemeriksaan->nama ?? '-' }}
                        </strong>

                        @if ($pemeriksaan && $pemeriksaan->kode)

                            <br>

                            <small>
                                Kode: {{ $pemeriksaan->kode }}
                            </small>

                        @endif

                    </td>

                    <td class="center">
                        {{ $hasil->hasil ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $pemeriksaan->satuan ?? '-' }}
                    </td>

                    <td>
                        {{ $pemeriksaan->nilai_rujukan ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $hasil->flag ?? '-' }}
                    </td>

                    <td>
                        {{ $hasil->catatan ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="center">
                        Tidak terdapat data pemeriksaan.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- STATUS --}}

    <div class="status-box">

        <div class="status-title">
            Status Pemeriksaan
        </div>

        <strong>
            {{ $labPermintaan->status ?? '-' }}
        </strong>

    </div>


    {{-- CATATAN DOKTER --}}

    @if ($labPermintaan->catatan)

        <div class="notes">

            <div class="notes-title">
                Catatan Dokter
            </div>

            <div>
                {{ $labPermintaan->catatan }}
            </div>

        </div>

    @endif


    {{-- TANDA TANGAN --}}

    @php

        $verifiedBy = null;

        foreach ($labPermintaan->details as $detail) {

            if (
                $detail->hasil &&
                $detail->hasil->verifiedBy
            ) {

                $verifiedBy = $detail->hasil->verifiedBy;

                break;
            }

        }

    @endphp


    <div class="signature-container">

        <div class="signature">

            <div>
                Tangerang,
                {{ now()->format('d/m/Y') }}
            </div>

            <div>
                Petugas Laboratorium
            </div>

            <div class="signature-space"></div>

            <strong>
                __________________________
            </strong>

            <br>

            @if ($verifiedBy)

                {{ $verifiedBy->name }}

            @else

                Petugas Laboratorium

            @endif

        </div>

    </div>


    {{-- FOOTER --}}

    <div class="footer">

        Dokumen ini dihasilkan oleh SIMRS.

    </div>

</body>

</html>
<template>
    <div>

        <!-- ================================================= -->
        <!-- HEADER -->
        <!-- ================================================= -->

        <div class="mb-6">

            <div
                class="flex flex-col md:flex-row
                       md:items-center
                       md:justify-between
                       gap-4"
            >

                <div>

                    <h1
                        class="text-2xl
                               font-bold
                               text-slate-800"
                    >
                        Riwayat Pemeriksaan
                    </h1>

                    <p
                        class="mt-1
                               text-slate-500"
                    >
                        Daftar pasien yang pernah diperiksa oleh Anda.
                    </p>

                </div>

                <button
                    @click="getRiwayat"
                    class="px-4 py-2
                           rounded-lg
                           bg-blue-600
                           text-white
                           text-sm
                           font-medium
                           hover:bg-blue-700
                           transition"
                >
                    ↻ Refresh
                </button>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- ERROR -->
        <!-- ================================================= -->

        <div
            v-if="error"
            class="mb-5
                   p-4
                   rounded-xl
                   bg-red-50
                   border border-red-200
                   text-red-700"
        >
            {{ error }}
        </div>


        <!-- ================================================= -->
        <!-- LOADING -->
        <!-- ================================================= -->

        <div
            v-if="loading"
            class="bg-white
                   rounded-2xl
                   border border-slate-200
                   p-10
                   text-center"
        >

            <div
                class="text-3xl
                       mb-3"
            >
                ⏳
            </div>

            <p class="text-slate-500">
                Memuat data pasien...
            </p>

        </div>


        <!-- ================================================= -->
        <!-- CONTENT -->
        <!-- ================================================= -->

        <div
            v-else
            class="space-y-5"
        >


            <!-- ================================================= -->
            <!-- SUMMARY -->
            <!-- ================================================= -->

            <div
                class="grid
                       grid-cols-1
                       md:grid-cols-3
                       gap-4"
            >

                <!-- TOTAL PASIEN -->

                <div
                    class="bg-white
                           rounded-2xl
                           border border-slate-200
                           p-5"
                >

                    <div
                        class="flex
                               items-center
                               justify-between"
                    >

                        <div>

                            <p
                                class="text-sm
                                       text-slate-500"
                            >
                                Total Pasien
                            </p>

                            <p
                                class="text-3xl
                                       font-bold
                                       text-slate-800
                                       mt-1"
                            >
                                {{ pasienList.length }}
                            </p>

                        </div>

                        <div
                            class="w-12 h-12
                                   rounded-xl
                                   bg-blue-50
                                   flex
                                   items-center
                                   justify-center
                                   text-2xl"
                        >
                            🧑
                        </div>

                    </div>

                </div>


                <!-- TOTAL PEMERIKSAAN -->

                <div
                    class="bg-white
                           rounded-2xl
                           border border-slate-200
                           p-5"
                >

                    <div
                        class="flex
                               items-center
                               justify-between"
                    >

                        <div>

                            <p
                                class="text-sm
                                       text-slate-500"
                            >
                                Total Pemeriksaan
                            </p>

                            <p
                                class="text-3xl
                                       font-bold
                                       text-slate-800
                                       mt-1"
                            >
                                {{ pemeriksaans.length }}
                            </p>

                        </div>

                        <div
                            class="w-12 h-12
                                   rounded-xl
                                   bg-green-50
                                   flex
                                   items-center
                                   justify-center
                                   text-2xl"
                        >
                            🩺
                        </div>

                    </div>

                </div>


                <!-- PASIEN DIPILIH -->

                <div
                    class="bg-white
                           rounded-2xl
                           border border-slate-200
                           p-5"
                >

                    <div
                        class="flex
                               items-center
                               justify-between"
                    >

                        <div>

                            <p
                                class="text-sm
                                       text-slate-500"
                            >
                                Pasien Dipilih
                            </p>

                            <p
                                class="text-lg
                                       font-bold
                                       text-slate-800
                                       mt-1
                                       truncate
                                       max-w-[180px]"
                            >
                                {{
                                    selectedPasien?.nama
                                    || '-'
                                }}
                            </p>

                        </div>

                        <div
                            class="w-12 h-12
                                   rounded-xl
                                   bg-purple-50
                                   flex
                                   items-center
                                   justify-center
                                   text-2xl"
                        >
                            📋
                        </div>

                    </div>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- DAFTAR PASIEN -->
            <!-- ================================================= -->

            <div
                class="bg-white
                       rounded-2xl
                       border border-slate-200
                       overflow-hidden"
            >

                <!-- TABLE HEADER -->

                <div
                    class="px-6 py-5
                           border-b border-slate-200"
                >

                    <div
                        class="flex
                               flex-col
                               md:flex-row
                               md:items-center
                               md:justify-between
                               gap-3"
                    >

                        <div>

                            <h2
                                class="text-lg
                                       font-bold
                                       text-slate-800"
                            >
                                Daftar Pasien
                            </h2>

                            <p
                                class="text-sm
                                       text-slate-500
                                       mt-1"
                            >
                                Pasien yang pernah mendapatkan pemeriksaan dari Anda.
                            </p>

                        </div>


                        <!-- SEARCH -->

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari pasien / No. RM..."
                            class="w-full
                                   md:w-72
                                   px-4 py-2.5
                                   rounded-xl
                                   border border-slate-300
                                   text-sm
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-500"
                        />

                    </div>

                </div>


                <!-- EMPTY -->

                <div
                    v-if="filteredPasien.length === 0"
                    class="p-10
                           text-center"
                >

                    <div
                        class="text-5xl
                               mb-4"
                    >
                        📋
                    </div>

                    <p
                        class="font-semibold
                               text-slate-700"
                    >
                        Belum ada pasien
                    </p>

                    <p
                        class="text-sm
                               text-slate-500
                               mt-1"
                    >
                        Belum terdapat pasien yang pernah diperiksa oleh dokter ini.
                    </p>

                </div>


                <!-- TABLE -->

                <div
                    v-else
                    class="overflow-x-auto"
                >

                    <table class="w-full text-sm">

                        <thead
                            class="bg-slate-50"
                        >

                            <tr>

                                <th
                                    class="px-6 py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    #
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    No. RM
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    Nama Pasien
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    Jenis Kelamin
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    Total Kunjungan
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    Kunjungan Terakhir
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-right
                                           font-semibold
                                           text-slate-500"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-for="(
                                    item,
                                    index
                                ) in filteredPasien"
                                :key="item.id"
                                class="border-t
                                       border-slate-100
                                       hover:bg-slate-50"
                            >

                                <!-- NO -->

                                <td
                                    class="px-6 py-4
                                           text-slate-500"
                                >
                                    {{ index + 1 }}
                                </td>


                                <!-- NO RM -->

                                <td
                                    class="px-6 py-4"
                                >

                                    <span
                                        class="font-semibold
                                               text-blue-600"
                                    >
                                        {{ item.no_rm || '-' }}
                                    </span>

                                </td>


                                <!-- NAMA -->

                                <td
                                    class="px-6 py-4"
                                >

                                    <div
                                        class="flex
                                               items-center
                                               gap-3"
                                    >

                                        <div
                                            class="w-9 h-9
                                                   rounded-full
                                                   bg-blue-100
                                                   text-blue-700
                                                   flex
                                                   items-center
                                                   justify-center
                                                   font-bold"
                                        >
                                            {{
                                                item.nama
                                                    ?.charAt(0)
                                                    ?.toUpperCase()
                                            }}
                                        </div>

                                        <div>

                                            <p
                                                class="font-semibold
                                                       text-slate-800"
                                            >
                                                {{ item.nama }}
                                            </p>

                                            <p
                                                class="text-xs
                                                       text-slate-500"
                                            >
                                                {{ item.no_telepon || '-' }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                <!-- JENIS KELAMIN -->

                                <td
                                    class="px-6 py-4
                                           text-slate-600"
                                >

                                    {{
                                        item.jenis_kelamin === 'L'
                                            ? 'Laki-laki'
                                            : 'Perempuan'
                                    }}

                                </td>


                                <!-- TOTAL KUNJUNGAN -->

                                <td
                                    class="px-6 py-4"
                                >

                                    <span
                                        class="inline-flex
                                               px-3 py-1
                                               rounded-full
                                               bg-blue-50
                                               text-blue-700
                                               font-semibold"
                                    >
                                        {{
                                            item.total_pemeriksaan
                                        }}
                                        kali
                                    </span>

                                </td>


                                <!-- KUNJUNGAN TERAKHIR -->

                                <td
                                    class="px-6 py-4
                                           text-slate-600"
                                >

                                    {{
                                        formatTanggal(
                                            item.kunjungan_terakhir
                                        )
                                    }}

                                </td>


                                <!-- AKSI -->

                                <td
                                    class="px-6 py-4
                                           text-right"
                                >

                                    <button
                                        @click="
                                            pilihPasien(item)
                                        "
                                        class="px-4 py-2
                                               rounded-lg
                                               bg-blue-600
                                               text-white
                                               text-sm
                                               font-medium
                                               hover:bg-blue-700
                                               transition"
                                    >
                                        Lihat Riwayat
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- DETAIL RIWAYAT PASIEN -->
            <!-- ================================================= -->

            <div
                v-if="selectedPasien"
                class="bg-white
                       rounded-2xl
                       border border-slate-200
                       overflow-hidden"
            >

                <!-- HEADER DETAIL -->

                <div
                    class="px-6 py-5
                           bg-slate-50
                           border-b border-slate-200"
                >

                    <div
                        class="flex
                               flex-col
                               md:flex-row
                               md:items-center
                               md:justify-between
                               gap-4"
                    >

                        <div>

                            <p
                                class="text-sm
                                       text-slate-500"
                            >
                                Riwayat Pasien
                            </p>

                            <h2
                                class="text-xl
                                       font-bold
                                       text-slate-800
                                       mt-1"
                            >
                                {{ selectedPasien.nama }}
                            </h2>

                            <p
                                class="text-sm
                                       text-slate-500
                                       mt-1"
                            >
                                No. RM:
                                {{ selectedPasien.no_rm || '-' }}

                                ·

                                {{ selectedPasien.total_pemeriksaan }}
                                pemeriksaan
                            </p>

                        </div>


                        <button
                            @click="selectedPasien = null"
                            class="px-4 py-2
                                   rounded-lg
                                   bg-white
                                   border border-slate-300
                                   text-slate-600
                                   text-sm
                                   hover:bg-slate-100"
                        >
                            Tutup
                        </button>

                    </div>

                </div>


                <!-- DETAIL LOADING -->

                <div
                    v-if="detailLoading"
                    class="p-10
                           text-center
                           text-slate-500"
                >
                    Memuat riwayat pasien...
                </div>


                <!-- DETAIL ERROR -->

                <div
                    v-else-if="detailError"
                    class="m-6
                           p-4
                           rounded-xl
                           bg-red-50
                           border border-red-200
                           text-red-700"
                >
                    {{ detailError }}
                </div>


                <!-- DETAIL EMPTY -->

                <div
                    v-else-if="detailPemeriksaans.length === 0"
                    class="p-10
                           text-center"
                >

                    <div
                        class="text-4xl
                               mb-3"
                    >
                        📋
                    </div>

                    <p
                        class="font-semibold
                               text-slate-700"
                    >
                        Belum ada detail pemeriksaan
                    </p>

                </div>


                <!-- DETAIL DATA -->

                <div
                    v-else
                    class="p-6
                           space-y-5"
                >

                    <div
                        v-for="pemeriksaan in detailPemeriksaans"
                        :key="pemeriksaan.id"
                        class="border
                               border-slate-200
                               rounded-2xl
                               overflow-hidden"
                    >

                        <!-- RIWAYAT HEADER -->

                        <div
                            class="px-5 py-4
                                   bg-slate-50
                                   border-b
                                   border-slate-200"
                        >

                            <div
                                class="flex
                                       flex-col
                                       md:flex-row
                                       md:items-center
                                       md:justify-between
                                       gap-3"
                            >

                                <div>

                                    <p
                                        class="font-bold
                                               text-slate-800"
                                    >
                                        {{
                                            formatTanggal(
                                                pemeriksaan
                                                    .pendaftaran
                                                    ?.tanggal_kunjungan
                                            )
                                        }}
                                    </p>

                                    <p
                                        class="text-sm
                                               text-slate-500
                                               mt-1"
                                    >

                                        Antrian:
                                        {{
                                            pemeriksaan
                                                .pendaftaran
                                                ?.no_antrian
                                                || '-'
                                        }}

                                        ·

                                        Poli:
                                        {{
                                            pemeriksaan
                                                .pendaftaran
                                                ?.poli
                                                ?.nama
                                                || '-'
                                        }}

                                        ·

                                        Dokter:
                                        {{
                                            pemeriksaan
                                                .pendaftaran
                                                ?.dokter
                                                ?.nama
                                                || '-'
                                        }}

                                    </p>

                                </div>


                                <span
                                    class="px-3 py-1
                                           rounded-full
                                           bg-green-100
                                           text-green-700
                                           text-xs
                                           font-semibold
                                           w-fit"
                                >
                                    Diperiksa
                                </span>

                            </div>

                        </div>


                        <!-- TANDA VITAL -->

                        <div class="p-5">

                            <h4
                                class="font-semibold
                                       text-slate-800
                                       mb-3"
                            >
                                Tanda Vital
                            </h4>


                            <div
                                class="grid
                                       grid-cols-2
                                       md:grid-cols-3
                                       lg:grid-cols-6
                                       gap-3"
                            >

                                <div
                                    class="p-3
                                           rounded-xl
                                           bg-slate-50"
                                >

                                    <p
                                        class="text-xs
                                               text-slate-500"
                                    >
                                        Tekanan Darah
                                    </p>

                                    <p
                                        class="font-semibold
                                               text-slate-800
                                               mt-1"
                                    >
                                        {{
                                            pemeriksaan
                                                .tekanan_darah
                                                || '-'
                                        }}
                                    </p>

                                </div>


                                <div
                                    class="p-3
                                           rounded-xl
                                           bg-slate-50"
                                >

                                    <p
                                        class="text-xs
                                               text-slate-500"
                                    >
                                        Suhu
                                    </p>

                                    <p
                                        class="font-semibold
                                               text-slate-800
                                               mt-1"
                                    >
                                        {{
                                            pemeriksaan.suhu
                                                || '-'
                                        }}

                                        <span
                                            v-if="
                                                pemeriksaan.suhu
                                            "
                                        >
                                            °C
                                        </span>

                                    </p>

                                </div>


                                <div
                                    class="p-3
                                           rounded-xl
                                           bg-slate-50"
                                >

                                    <p
                                        class="text-xs
                                               text-slate-500"
                                    >
                                        Berat Badan
                                    </p>

                                    <p
                                        class="font-semibold
                                               text-slate-800
                                               mt-1"
                                    >
                                        {{
                                            pemeriksaan
                                                .berat_badan
                                                || '-'
                                        }}

                                        <span
                                            v-if="
                                                pemeriksaan.berat_badan
                                            "
                                        >
                                            kg
                                        </span>

                                    </p>

                                </div>


                                <div
                                    class="p-3
                                           rounded-xl
                                           bg-slate-50"
                                >

                                    <p
                                        class="text-xs
                                               text-slate-500"
                                    >
                                        Tinggi Badan
                                    </p>

                                    <p
                                        class="font-semibold
                                               text-slate-800
                                               mt-1"
                                    >
                                        {{
                                            pemeriksaan
                                                .tinggi_badan
                                                || '-'
                                        }}

                                        <span
                                            v-if="
                                                pemeriksaan.tinggi_badan
                                            "
                                        >
                                            cm
                                        </span>

                                    </p>

                                </div>


                                <div
                                    class="p-3
                                           rounded-xl
                                           bg-slate-50"
                                >

                                    <p
                                        class="text-xs
                                               text-slate-500"
                                    >
                                        Nadi
                                    </p>

                                    <p
                                        class="font-semibold
                                               text-slate-800
                                               mt-1"
                                    >
                                        {{
                                            pemeriksaan.nadi
                                                || '-'
                                        }}

                                        <span
                                            v-if="
                                                pemeriksaan.nadi
                                            "
                                        >
                                            x/menit
                                        </span>

                                    </p>

                                </div>


                                <div
                                    class="p-3
                                           rounded-xl
                                           bg-slate-50"
                                >

                                    <p
                                        class="text-xs
                                               text-slate-500"
                                    >
                                        Respirasi
                                    </p>

                                    <p
                                        class="font-semibold
                                               text-slate-800
                                               mt-1"
                                    >
                                        {{
                                            pemeriksaan.respirasi
                                                || '-'
                                        }}

                                        <span
                                            v-if="
                                                pemeriksaan.respirasi
                                            "
                                        >
                                            x/menit
                                        </span>

                                    </p>

                                </div>

                            </div>


                            <!-- HASIL -->

                            <div
                                class="grid
                                       grid-cols-1
                                       md:grid-cols-3
                                       gap-4
                                       mt-6"
                            >

                                <div>

                                    <p
                                        class="text-sm
                                               font-semibold
                                               text-slate-700
                                               mb-2"
                                    >
                                        Diagnosis
                                    </p>

                                    <div
                                        class="p-4
                                               rounded-xl
                                               bg-slate-50
                                               text-sm
                                               text-slate-700
                                               min-h-[80px]"
                                    >
                                        {{
                                            pemeriksaan.diagnosis
                                            || '-'
                                        }}
                                    </div>

                                </div>


                                <div>

                                    <p
                                        class="text-sm
                                               font-semibold
                                               text-slate-700
                                               mb-2"
                                    >
                                        Tindakan
                                    </p>

                                    <div
                                        class="p-4
                                               rounded-xl
                                               bg-slate-50
                                               text-sm
                                               text-slate-700
                                               min-h-[80px]"
                                    >
                                        {{
                                            pemeriksaan.tindakan
                                            || '-'
                                        }}
                                    </div>

                                </div>


                                <div>

                                    <p
                                        class="text-sm
                                               font-semibold
                                               text-slate-700
                                               mb-2"
                                    >
                                        Catatan
                                    </p>

                                    <div
                                        class="p-4
                                               rounded-xl
                                               bg-slate-50
                                               text-sm
                                               text-slate-700
                                               min-h-[80px]"
                                    >
                                        {{
                                            pemeriksaan.catatan
                                            || '-'
                                        }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</template>


<script setup>

import {
    ref,
    computed,
    onMounted
} from 'vue';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const loading = ref(false);

const error = ref('');

const pemeriksaans = ref([]);

const pasienList = ref([]);

const search = ref('');

const selectedPasien = ref(null);

const detailPemeriksaans = ref([]);

const detailLoading = ref(false);

const detailError = ref('');


/*
|--------------------------------------------------------------------------
| Ambil semua pemeriksaan dokter yang sedang login
|--------------------------------------------------------------------------
*/

const getRiwayat = async () => {

    try {

        loading.value = true;

        error.value = '';


        const response = await axios.get(
            '/api/dokter/pasien-riwayat'
        );


        pemeriksaans.value =
            response.data.pemeriksaans || [];


        /*
        |--------------------------------------------------------------------------
        | Kelompokkan pemeriksaan berdasarkan pasien
        |--------------------------------------------------------------------------
        */

        const pasienMap = new Map();


        pemeriksaans.value.forEach(
            (pemeriksaan) => {

                const pasien =
                    pemeriksaan
                        .pendaftaran
                        ?.pasien;


                if (!pasien) {
                    return;
                }


                if (!pasienMap.has(pasien.id)) {

                    pasienMap.set(
                        pasien.id,
                        {
                            ...pasien,

                            total_pemeriksaan: 0,

                            kunjungan_terakhir:
                                null
                        }
                    );

                }


                const item =
                    pasienMap.get(
                        pasien.id
                    );


                item.total_pemeriksaan++;


                const tanggal =
                    pemeriksaan
                        .pendaftaran
                        ?.tanggal_kunjungan;


                if (
                    tanggal &&
                    (
                        !item.kunjungan_terakhir ||
                        new Date(tanggal) >
                        new Date(
                            item.kunjungan_terakhir
                        )
                    )
                ) {

                    item.kunjungan_terakhir =
                        tanggal;

                }

            }
        );


        pasienList.value =
            Array.from(
                pasienMap.values()
            );


    } catch (err) {

        console.error(
            'Gagal mengambil riwayat dokter:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Gagal mengambil data riwayat pemeriksaan.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Search pasien
|--------------------------------------------------------------------------
*/

const filteredPasien = computed(() => {

    const keyword =
        search.value
            .trim()
            .toLowerCase();


    if (!keyword) {

        return pasienList.value;

    }


    return pasienList.value.filter(
        (pasien) => {

            return (

                pasien.nama
                    ?.toLowerCase()
                    .includes(keyword)

                ||

                pasien.no_rm
                    ?.toLowerCase()
                    .includes(keyword)

            );

        }
    );

});


/*
|--------------------------------------------------------------------------
| Pilih pasien
|--------------------------------------------------------------------------
*/

const pilihPasien = async (pasien) => {

    selectedPasien.value = pasien;

    detailPemeriksaans.value = [];

    detailError.value = '';


    try {

        detailLoading.value = true;


        /*
        |--------------------------------------------------------------------------
        | Ambil seluruh riwayat pasien
        |--------------------------------------------------------------------------
        |
        | Endpoint ini mengambil semua pemeriksaan
        | pasien tersebut.
        |
        */

        const response = await axios.get(
            `/api/pasien/${pasien.id}/riwayat-pemeriksaan`
        );


        /*
        |--------------------------------------------------------------------------
        | Filter lagi berdasarkan dokter login
        |--------------------------------------------------------------------------
        |
        | Supaya yang tampil hanya pemeriksaan
        | yang dilakukan dokter yang sedang login.
        |
        */

        const semua =
            response.data.pemeriksaans || [];


        detailPemeriksaans.value =
            semua.filter(
                (pemeriksaan) => {

                    /*
                    |--------------------------------------------------------------------------
                    | Karena endpoint pasien mengembalikan
                    | semua dokter, kita cocokkan dokter.
                    |--------------------------------------------------------------------------
                    |
                    | ID dokter yang sedang login tidak dikirim
                    | dari frontend.
                    |
                    | Endpoint /api/dokter/pasien-riwayat
                    | sudah merupakan sumber data dokter login.
                    |
                    */

                    return pemeriksaans.value.some(
                        (item) =>
                            item.id ===
                            pemeriksaan.id
                    );

                }
            );


    } catch (err) {

        console.error(
            'Gagal mengambil detail pasien:',
            err
        );


        detailError.value =
            err.response?.data?.message ||
            'Gagal mengambil riwayat pasien.';

    } finally {

        detailLoading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Format tanggal
|--------------------------------------------------------------------------
*/

const formatTanggal = (tanggal) => {

    if (!tanggal) {

        return '-';

    }


    return new Date(
        tanggal
    ).toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }
    );

};


/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(() => {

    getRiwayat();

});

</script>
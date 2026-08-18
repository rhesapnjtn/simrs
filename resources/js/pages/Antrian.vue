<script setup>

import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();


// =====================================================
// STATE
// =====================================================

const pendaftarans = ref([]);
const polis = ref([]);

const loading = ref(false);
const error = ref('');
const success = ref('');

const filterPoli = ref('');
const filterStatus = ref('');
const filterTanggal = ref('');


// =====================================================
// TANGGAL HARI INI
// =====================================================

const getToday = () => {

    const now = new Date();

    return (
        now.getFullYear() +
        '-' +
        String(now.getMonth() + 1).padStart(2, '0') +
        '-' +
        String(now.getDate()).padStart(2, '0')
    );

};


// =====================================================
// AMBIL DATA ANTREAN
// =====================================================

const getPendaftarans = async () => {

    try {

        loading.value = true;
        error.value = '';

        if (!filterTanggal.value) {
            filterTanggal.value = getToday();
        }

        const params = {

            tanggal:
                filterTanggal.value

        };


        if (filterPoli.value) {

            params.poli_id =
                filterPoli.value;

        }


        const response =
            await axios.get(
                '/api/pendaftarans',
                {
                    params
                }
            );


        pendaftarans.value =
            response.data.pendaftarans ||
            [];

    } catch (err) {

        console.error(
            'Gagal mengambil antrean:',
            err
        );

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil data antrean.';

    } finally {

        loading.value = false;

    }

};


// =====================================================
// AMBIL DATA POLI
// =====================================================

const getPolis = async () => {

    try {

        const response =
            await axios.get(
                '/api/polis'
            );

        polis.value =
            response.data.polis ||
            [];

    } catch (err) {

        console.error(
            'Gagal mengambil data poli:',
            err
        );

    }

};


// =====================================================
// FILTER
// =====================================================

const filteredPendaftarans =
    computed(() => {

        if (!filterStatus.value) {

            return pendaftarans.value;

        }

        return pendaftarans.value.filter(
            pendaftaran =>
                pendaftaran.status ===
                filterStatus.value
        );

    });


// =====================================================
// SUARA ANTREAN
// =====================================================

const panggilSuara = (
    pendaftaran
) => {

    const nomor =
        pendaftaran.no_antrian || '';

    const nama =
        pendaftaran.pasien?.nama || '';

    const poli =
        pendaftaran.poli?.nama || '';


    const text =
        `Nomor antrean ${nomor}, ` +
        `atas nama ${nama}, ` +
        `silakan menuju poli ${poli}.`;


    const utterance =
        new SpeechSynthesisUtterance(
            text
        );


    utterance.lang =
        'id-ID';

    utterance.rate =
        0.85;

    utterance.pitch =
        1;


    window.speechSynthesis.cancel();

    window.speechSynthesis.speak(
        utterance
    );

};


// =====================================================
// UPDATE STATUS
// =====================================================

const updateStatus = async (
    pendaftaran,
    status
) => {

    try {

        error.value = '';
        success.value = '';


        await axios.put(
            `/api/pendaftarans/${pendaftaran.id}/status`,
            {
                status
            }
        );


        await getPendaftarans();


        success.value =
            `Status antrean ${pendaftaran.no_antrian} berhasil diperbarui.`;


    } catch (err) {

        console.error(
            'Gagal update status:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Gagal memperbarui status antrean.';


        throw err;

    }

};


// =====================================================
// PANGGIL + UPDATE
// =====================================================

const panggilDanUpdate = async (
    pendaftaran
) => {

    try {

        error.value = '';
        success.value = '';


        await updateStatus(
            pendaftaran,
            'DIPANGGIL'
        );


        panggilSuara(
            pendaftaran
        );


        success.value =
            `Antrean ${pendaftaran.no_antrian} dipanggil.`;

    } catch (err) {

        console.error(
            'Gagal memanggil antrean:',
            err
        );

    }

};


// =====================================================
// MULAI PEMERIKSAAN
// =====================================================

const mulaiPeriksa = async (
    pendaftaran
) => {

    try {

        error.value = '';
        success.value = '';


        // Ubah status menjadi DIPERIKSA
        await updateStatus(
            pendaftaran,
            'DIPERIKSA'
        );


        // Setelah berhasil,
        // buka halaman pemeriksaan
        router.push(
            `/pendaftarans/${pendaftaran.id}/pemeriksaan`
        );


    } catch (err) {

        console.error(
            'Gagal memulai pemeriksaan:',
            err
        );

    }

};





// =====================================================
// STATUS LABEL
// =====================================================

const statusLabel = (
    status
) => {

    const labels = {

        MENUNGGU:
            'Menunggu',

        DIPANGGIL:
            'Dipanggil',

        DIPERIKSA:
            'Diperiksa',

        SELESAI:
            'Selesai',

        BATAL:
            'Batal'

    };


    return (
        labels[status] ||
        status
    );

};


// =====================================================
// STATUS CLASS
// =====================================================

const statusClass = (
    status
) => {

    const classes = {

        MENUNGGU:
            'bg-yellow-100 text-yellow-700',

        DIPANGGIL:
            'bg-blue-100 text-blue-700',

        DIPERIKSA:
            'bg-purple-100 text-purple-700',

        SELESAI:
            'bg-green-100 text-green-700',

        BATAL:
            'bg-red-100 text-red-700'

    };


    return (
        classes[status] ||
        'bg-slate-100 text-slate-600'
    );

};


// =====================================================
// RESET FILTER
// =====================================================

const resetFilter = () => {

    filterPoli.value = '';
    filterStatus.value = '';
    filterTanggal.value =
        getToday();

    getPendaftarans();

};


// =====================================================
// INIT
// =====================================================

onMounted(() => {

    getPolis();

    getPendaftarans();

});

</script>



<template>

<div class="min-h-screen bg-slate-100">

    <!-- ================================================= -->
    <!-- NAVBAR -->
    <!-- ================================================= -->

    <header
        class="bg-white
               border-b border-slate-200"
    >

        <div
            class="h-16 px-6
                   flex items-center
                   justify-between"
        >

            <div class="flex items-center gap-3">

                <div
                    class="w-9 h-9
                           rounded-lg
                           bg-blue-600
                           flex items-center
                           justify-center
                           text-white
                           font-bold"
                >
                    S
                </div>

                <div>

                    <h1
                        class="font-bold
                               text-slate-800"
                    >
                        SIMRS
                    </h1>

                    <p
                        class="text-xs
                               text-slate-500"
                    >
                        Hospital Management System
                    </p>

                </div>

            </div>


            <button
                @click="
                    router.push('/dashboard')
                "
                class="text-sm
                       text-slate-600
                       hover:text-blue-600"
            >
                ← Dashboard
            </button>

        </div>

    </header>


    <!-- ================================================= -->
    <!-- CONTENT -->
    <!-- ================================================= -->

    <main class="p-6">

        <!-- Header -->

        <div
            class="flex flex-col
                   md:flex-row
                   md:items-center
                   md:justify-between
                   gap-4 mb-6"
        >

            <div>

                <h2
                    class="text-2xl
                           font-bold
                           text-slate-800"
                >
                    Manajemen Antrean
                </h2>

                <p
                    class="text-slate-500
                           mt-1"
                >
                    Kelola antrean pasien hari ini.
                </p>

            </div>


            <button
                @click="getPendaftarans"
                :disabled="loading"
                class="px-4 py-2
                       rounded-xl
                       border border-slate-300
                       bg-white
                       text-slate-700
                       hover:bg-slate-50
                       disabled:opacity-50"
            >
                {{ loading ? 'Memuat...' : '↻ Refresh' }}
            </button>

        </div>


        <!-- ================================================= -->
        <!-- ALERT -->
        <!-- ================================================= -->

        <div
            v-if="error"
            class="mb-5 p-4
                   rounded-xl
                   bg-red-50
                   border border-red-200
                   text-red-700"
        >
            {{ error }}
        </div>


        <div
            v-if="success"
            class="mb-5 p-4
                   rounded-xl
                   bg-green-50
                   border border-green-200
                   text-green-700"
        >
            {{ success }}
        </div>


        <!-- ================================================= -->
<!-- FILTER -->
<!-- ================================================= -->

<div
    class="bg-white
           rounded-2xl
           border border-slate-200
           p-5 mb-6"
>

    <div
        class="grid grid-cols-1
               md:grid-cols-4
               gap-4"
    >

        <!-- Tanggal Kunjungan -->

        <div>

            <label
                class="block text-sm
                       font-medium
                       text-slate-700
                       mb-2"
            >
                Tanggal Kunjungan
            </label>

            <input
                v-model="filterTanggal"
                @change="getPendaftarans"
                type="date"
                class="w-full
                       px-4 py-3
                       rounded-xl
                       border border-slate-300
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-500"
            >

        </div>


        <!-- Poli -->

        <div>

            <label
                class="block text-sm
                       font-medium
                       text-slate-700
                       mb-2"
            >
                Filter Poli
            </label>

            <select
                v-model="filterPoli"
                @change="getPendaftarans"
                class="w-full
                       px-4 py-3
                       rounded-xl
                       border border-slate-300
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-500"
            >

                <option value="">
                    Semua Poli
                </option>

                <option
                    v-for="poli in polis"
                    :key="poli.id"
                    :value="poli.id"
                >
                    {{ poli.nama }}
                </option>

            </select>

        </div>


        <!-- Status -->

        <div>

            <label
                class="block text-sm
                       font-medium
                       text-slate-700
                       mb-2"
            >
                Filter Status
            </label>

            <select
                v-model="filterStatus"
                class="w-full
                       px-4 py-3
                       rounded-xl
                       border border-slate-300
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-500"
            >

                <option value="">
                    Semua Status
                </option>

                <option value="MENUNGGU">
                    Menunggu
                </option>

                <option value="DIPANGGIL">
                    Dipanggil
                </option>

                <option value="DIPERIKSA">
                    Diperiksa
                </option>

                <option value="SELESAI">
                    Selesai
                </option>

                <option value="BATAL">
                    Batal
                </option>

            </select>

        </div>


        <!-- Reset -->

        <div class="flex items-end">

            <button
                @click="resetFilter"
                class="w-full
                       px-4 py-3
                       rounded-xl
                       border border-slate-300
                       text-slate-700
                       hover:bg-slate-50"
            >
                Reset Filter
            </button>

        </div>

    </div>

</div>

        <!-- ================================================= -->
        <!-- TABLE -->
        <!-- ================================================= -->

        <div
            class="bg-white
                   rounded-2xl
                   border border-slate-200
                   overflow-hidden"
        >

            <!-- Loading -->

            <div
                v-if="loading"
                class="p-10
                       text-center
                       text-slate-500"
            >
                Memuat antrean...
            </div>


            <!-- Empty -->

            <div
                v-else-if="
                    filteredPendaftarans.length === 0
                "
                class="p-10
                       text-center
                       text-slate-500"
            >

                <div
                    class="text-4xl mb-3"
                >
                    📋
                </div>

                <p
                    class="font-medium
                           text-slate-700"
                >
                    Belum ada antrean
                </p>

                <p
                    class="text-sm
                           mt-1"
                >
                    Belum ada pasien yang terdaftar
                    untuk hari ini.

                </p>

            </div>


            <!-- Table -->

            <div
                v-else
                class="overflow-x-auto"
            >

                <table
                    class="w-full text-sm"
                >

                    <thead
                        class="bg-slate-50"
                    >

                        <tr>

                            <th
                                class="text-left
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Antrean
                            </th>

                            <th
                                class="text-left
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Pasien
                            </th>

                            <th
                                class="text-left
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Poli
                            </th>

                            <th
                                class="text-left
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Dokter
                            </th>

                            <th
                                class="text-left
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Status
                            </th>

                            <th
                                class="text-right
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="
                                pendaftaran
                                in filteredPendaftarans
                            "
                            :key="pendaftaran.id"
                            class="border-t
                                   border-slate-100
                                   hover:bg-slate-50"
                        >

                            <!-- Antrean -->

                            <td
                                class="px-5 py-5"
                            >

                                <span
                                    class="text-xl
                                           font-bold
                                           text-blue-600"
                                >
                                    {{
                                        pendaftaran.no_antrian
                                    }}
                                </span>

                            </td>


                            <!-- Pasien -->

                            <td
                                class="px-5 py-5"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-800"
                                >
                                    {{
                                        pendaftaran
                                            .pasien
                                            ?.nama
                                    }}
                                </p>

                                <p
                                    class="text-xs
                                           text-slate-500
                                           mt-1"
                                >
                                    {{
                                        pendaftaran
                                            .pasien
                                            ?.no_rm
                                    }}
                                </p>

                            </td>


                            <!-- Poli -->

                            <td
                                class="px-5 py-5"
                            >

                                {{
                                    pendaftaran
                                        .poli
                                        ?.nama
                                }}

                            </td>


                            <!-- Dokter -->

                            <td
                                class="px-5 py-5"
                            >

                                {{
                                    pendaftaran
                                        .dokter
                                        ?.nama
                                }}

                            </td>


                            <!-- Status -->

                            <td
                                class="px-5 py-5"
                            >

                                <span
                                    class="px-3 py-1
                                           rounded-full
                                           text-xs
                                           font-semibold"
                                    :class="
                                        statusClass(
                                            pendaftaran.status
                                        )
                                    "
                                >
                                    {{
                                        statusLabel(
                                            pendaftaran.status
                                        )
                                    }}
                                </span>

                            </td>


                            <!-- Actions -->

<td
    class="px-5 py-5 text-right"
>

    <div
        class="flex
               justify-end
               gap-2
               flex-wrap"
    >

        <!-- ================================================= -->
        <!-- MENUNGGU -->
        <!-- ================================================= -->

        <template
            v-if="
                pendaftaran.status === 'MENUNGGU'
            "
        >

            <!-- Panggil -->
            <button
                @click="
                    panggilDanUpdate(
                        pendaftaran
                    )
                "
                class="px-3 py-2
                       rounded-lg
                       bg-blue-600
                       text-white
                       text-xs
                       font-semibold
                       hover:bg-blue-700
                       transition"
            >
                🔊 Panggil
            </button>


            <!-- Batal -->
            <button
                @click="
                    updateStatus(
                        pendaftaran,
                        'BATAL'
                    )
                "
                class="px-3 py-2
                       rounded-lg
                       bg-red-50
                       text-red-600
                       text-xs
                       font-semibold
                       hover:bg-red-100
                       transition"
            >
                Batal
            </button>

        </template>


        <!-- ================================================= -->
        <!-- DIPANGGIL -->
        <!-- ================================================= -->

        <template
            v-else-if="
                pendaftaran.status === 'DIPANGGIL'
            "
        >

            <!-- Panggil Lagi -->
            <button
                @click="
                    panggilSuara(
                        pendaftaran
                    )
                "
                class="px-3 py-2
                       rounded-lg
                       bg-blue-50
                       text-blue-600
                       text-xs
                       font-semibold
                       hover:bg-blue-100
                       transition"
            >
                🔊 Panggil Lagi
            </button>


            


            <!-- Kembali -->
            <button
                @click="
                    updateStatus(
                        pendaftaran,
                        'MENUNGGU'
                    )
                "
                class="px-3 py-2
                       rounded-lg
                       bg-slate-100
                       text-slate-700
                       text-xs
                       font-semibold
                       hover:bg-slate-200
                       transition"
            >
                ← Kembali
            </button>

        </template>


        <!-- ================================================= -->
        <!-- DIPERIKSA -->
        <!-- ================================================= -->

        <template
            v-else-if="
                pendaftaran.status === 'DIPERIKSA'
            "
        >

            


            <!-- Kembali -->
            <button
                @click="
                    updateStatus(
                        pendaftaran,
                        'DIPANGGIL'
                    )
                "
                class="px-3 py-2
                       rounded-lg
                       bg-slate-100
                       text-slate-700
                       text-xs
                       font-semibold
                       hover:bg-slate-200
                       transition"
            >
                ← Kembali
            </button>


            <!-- Selesai -->
            <button
                @click="
                    updateStatus(
                        pendaftaran,
                        'SELESAI'
                    )
                "
                class="px-3 py-2
                       rounded-lg
                       bg-green-600
                       text-white
                       text-xs
                       font-semibold
                       hover:bg-green-700
                       transition"
            >
                ✓ Selesai
            </button>

        </template>


        <!-- ================================================= -->
        <!-- SELESAI -->
        <!-- ================================================= -->

        <template
            v-else-if="
                pendaftaran.status === 'SELESAI'
            "
        >

            <!-- Hasil Pemeriksaan -->
            <button
                @click="
                    lihatHasil(
                        pendaftaran.id
                    )
                "
                class="px-3 py-2
                       rounded-lg
                       bg-green-600
                       text-white
                       text-xs
                       font-semibold
                       hover:bg-green-700
                       transition"
            >
                📋 Hasil Pemeriksaan
            </button>


            <!-- Kembali -->
            <button
                @click="
                    updateStatus(
                        pendaftaran,
                        'DIPERIKSA'
                    )
                "
                class="px-3 py-2
                       rounded-lg
                       bg-slate-100
                       text-slate-700
                       text-xs
                       font-semibold
                       hover:bg-slate-200
                       transition"
            >
                ← Kembali
            </button>

        </template>


        <!-- ================================================= -->
        <!-- BATAL -->
        <!-- ================================================= -->

        <template
            v-else-if="
                pendaftaran.status === 'BATAL'
            "
        >

            <span
                class="px-3 py-2
                       text-xs
                       text-slate-400"
            >
                Dibatalkan
            </span>

        </template>

    </div>

</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</template>
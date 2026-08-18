<script setup>

import {
    ref,
    computed,
    onMounted,
    watch
} from 'vue';

import {
    useRoute,
    useRouter
} from 'vue-router';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/

const route = useRoute();
const router = useRouter();


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const loading = ref(false);
const saving = ref(false);

const error = ref('');
const success = ref('');

const pendaftarans = ref([]);

const pendaftaran = ref(null);
const pemeriksaan = ref(null);


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = ref({

    tekanan_darah: '',
    suhu: '',
    berat_badan: '',
    tinggi_badan: '',
    nadi: '',
    respirasi: '',
    diagnosis: '',
    tindakan: '',
    catatan: '',

});


/*
|--------------------------------------------------------------------------
| MODE DETAIL
|--------------------------------------------------------------------------
|
| /pemeriksaan
|   = daftar pasien
|
| /pendaftarans/8/pemeriksaan
|   = pemeriksaan pasien ID 8
|--------------------------------------------------------------------------
*/

const isDetail = computed(() => {

    return !!route.params.id;

});


/*
|--------------------------------------------------------------------------
| ID PENDAFTARAN
|--------------------------------------------------------------------------
*/

const pendaftaranId = computed(() => {

    return route.params.id;

});


/*
|--------------------------------------------------------------------------
| TANGGAL HARI INI
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| RESET FORM
|--------------------------------------------------------------------------
*/

const resetForm = () => {

    form.value = {

        tekanan_darah: '',
        suhu: '',
        berat_badan: '',
        tinggi_badan: '',
        nadi: '',
        respirasi: '',
        diagnosis: '',
        tindakan: '',
        catatan: '',

    };

};


/*
|--------------------------------------------------------------------------
| AMBIL DAFTAR PASIEN
|--------------------------------------------------------------------------
*/

const getPendaftarans = async () => {

    try {

        loading.value = true;
        error.value = '';
        success.value = '';

        const response = await axios.get(
            '/api/pendaftarans',
            {
                params: {
                    tanggal: getToday()
                }
            }
        );

        console.log(
            'DATA PENDAFTARAN:',
            response.data
        );


        /*
        |--------------------------------------------------------------------------
        | SUPPORT BEBERAPA FORMAT RESPONSE
        |--------------------------------------------------------------------------
        */

        if (Array.isArray(response.data)) {

            pendaftarans.value =
                response.data;

        } else {

            pendaftarans.value =
                response.data.pendaftarans ||
                response.data.data ||
                [];

        }

    } catch (err) {

        console.error(
            'Gagal mengambil daftar pemeriksaan:',
            err
        );

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil data pasien.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| PASIEN YANG SIAP DIPERIKSA
|--------------------------------------------------------------------------
*/

const pasienUntukDiperiksa = computed(() => {

    return pendaftarans.value.filter(item => {

        return (

            item.status === 'DIPANGGIL' ||

            item.status === 'DIPERIKSA'

        );

    });

});


/*
|--------------------------------------------------------------------------
| AMBIL DETAIL PEMERIKSAAN
|--------------------------------------------------------------------------
*/

const getPemeriksaan = async () => {

    if (!pendaftaranId.value) {

        return;

    }


    try {

        loading.value = true;

        error.value = '';

        success.value = '';

        pendaftaran.value = null;

        pemeriksaan.value = null;

        resetForm();


        console.log(
            'Mengambil pemeriksaan ID:',
            pendaftaranId.value
        );


        const response = await axios.get(
            `/api/pendaftarans/${pendaftaranId.value}/pemeriksaan`
        );


        console.log(
            'RESPONSE PEMERIKSAAN:',
            response.data
        );


        /*
        |--------------------------------------------------------------------------
        | AMBIL PENDAFTARAN
        |--------------------------------------------------------------------------
        */

        pendaftaran.value =
            response.data?.pendaftaran ||
            response.data?.data?.pendaftaran ||
            null;


        /*
        |--------------------------------------------------------------------------
        | AMBIL PEMERIKSAAN
        |--------------------------------------------------------------------------
        */

        pemeriksaan.value =
            response.data?.pemeriksaan ||
            response.data?.data?.pemeriksaan ||
            null;


        /*
        |--------------------------------------------------------------------------
        | DEBUG
        |--------------------------------------------------------------------------
        */

        console.log(
            'PENDAFTARAN:',
            pendaftaran.value
        );

        console.log(
            'PEMERIKSAAN:',
            pemeriksaan.value
        );


        /*
        |--------------------------------------------------------------------------
        | JIKA PEMERIKSAAN SUDAH ADA
        |--------------------------------------------------------------------------
        */

        if (pemeriksaan.value) {

            form.value = {

                tekanan_darah:
                    pemeriksaan.value.tekanan_darah ?? '',

                suhu:
                    pemeriksaan.value.suhu ?? '',

                berat_badan:
                    pemeriksaan.value.berat_badan ?? '',

                tinggi_badan:
                    pemeriksaan.value.tinggi_badan ?? '',

                nadi:
                    pemeriksaan.value.nadi ?? '',

                respirasi:
                    pemeriksaan.value.respirasi ?? '',

                diagnosis:
                    pemeriksaan.value.diagnosis ?? '',

                tindakan:
                    pemeriksaan.value.tindakan ?? '',

                catatan:
                    pemeriksaan.value.catatan ?? '',

            };

        }

    } catch (err) {

        console.error(
            'Gagal mengambil pemeriksaan:',
            err
        );


        if (err.response?.status === 404) {

            error.value =
                'Data pendaftaran tidak ditemukan.';

        } else {

            error.value =
                err.response?.data?.message ||
                'Gagal mengambil data pemeriksaan.';

        }

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| BUKA PEMERIKSAAN
|--------------------------------------------------------------------------
*/

const mulaiPemeriksaan = (item) => {

    router.push(
        `/pendaftarans/${item.id}/pemeriksaan`
    );

};


/*
|--------------------------------------------------------------------------
| SIMPAN PEMERIKSAAN
|--------------------------------------------------------------------------
*/

const simpanPemeriksaan = async () => {

    try {

        saving.value = true;
        error.value = '';
        success.value = '';

        console.log('DATA YANG DIKIRIM:', form.value);

        const response = await axios.post(
            `/api/pendaftarans/${pendaftaranId.value}/pemeriksaan`,
            form.value
        );

        console.log(
            'PEMERIKSAAN BERHASIL:',
            response.data
        );

        success.value =
            response.data.message ||
            'Pemeriksaan berhasil disimpan.';

        await getPemeriksaan();

    } catch (err) {

        console.error(
            'GAGAL SIMPAN PEMERIKSAAN:',
            err
        );

        console.error(
            'STATUS:',
            err.response?.status
        );

        console.error(
            'RESPONSE:',
            err.response?.data
        );

        console.error(
            'VALIDATION:',
            err.response?.data?.errors
        );

        if (err.response?.status === 422) {

            const errors =
                err.response?.data?.errors;

            if (errors) {

                error.value =
                    Object.entries(errors)
                        .map(([field, messages]) => {

                            return `${field}: ${messages.join(', ')}`;

                        })
                        .join(' | ');

            } else {

                error.value =
                    err.response?.data?.message ||
                    'Data pemeriksaan tidak valid.';

            }

        } else {

            error.value =
                err.response?.data?.message ||
                'Gagal menyimpan pemeriksaan.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| KEMBALI KE DAFTAR PEMERIKSAAN
|--------------------------------------------------------------------------
*/

const kembali = () => {

    router.push('/pemeriksaan');

};


/*
|--------------------------------------------------------------------------
| STATUS LABEL
|--------------------------------------------------------------------------
*/

const statusLabel = (status) => {

    const labels = {

        MENUNGGU: 'Menunggu',

        DIPANGGIL: 'Dipanggil',

        DIPERIKSA: 'Diperiksa',

        SELESAI: 'Selesai',

        BATAL: 'Batal',

    };


    return labels[status] || status;

};


/*
|--------------------------------------------------------------------------
| STATUS CLASS
|--------------------------------------------------------------------------
*/

const statusClass = (status) => {

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
            'bg-red-100 text-red-700',

    };


    return (
        classes[status] ||
        'bg-gray-100 text-gray-600'
    );

};


/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/

onMounted(() => {

    if (isDetail.value) {

        getPemeriksaan();

    } else {

        getPendaftarans();

    }

});


/*
|--------------------------------------------------------------------------
| JIKA ROUTE BERUBAH
|--------------------------------------------------------------------------
|
| Contoh:
|
| /pendaftarans/8/pemeriksaan
| menjadi
| /pendaftarans/9/pemeriksaan
|--------------------------------------------------------------------------
*/

watch(

    () => route.params.id,

    (newId, oldId) => {

        if (newId === oldId) {
            return;
        }


        if (newId) {

            getPemeriksaan();

        } else {

            getPendaftarans();

        }

    }

);

</script>


<template>

<div class="space-y-6">


    <!-- ===================================================== -->
    <!-- DAFTAR PEMERIKSAAN -->
    <!-- ===================================================== -->

    <template v-if="!isDetail">


        <!-- HEADER -->

        <div
            class="flex flex-col gap-4
                   md:flex-row
                   md:items-center
                   md:justify-between"
        >

            <div>

                <h1
                    class="text-2xl font-bold text-gray-800"
                >
                    Pemeriksaan Pasien
                </h1>

                <p
                    class="mt-1 text-sm text-gray-500"
                >
                    Daftar pasien yang siap
                    dilakukan pemeriksaan.
                </p>

            </div>


            <button
                @click="getPendaftarans"
                :disabled="loading"
                class="rounded-lg bg-blue-600
                       px-4 py-2 text-sm
                       font-semibold text-white
                       hover:bg-blue-700
                       disabled:opacity-50"
            >

                {{ loading ? 'Memuat...' : '↻ Refresh' }}

            </button>

        </div>


        <!-- ERROR -->

        <div
            v-if="error"
            class="rounded-xl border
                   border-red-200
                   bg-red-50
                   p-4 text-red-700"
        >

            {{ error }}

        </div>


        <!-- TABLE -->

        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200
                   bg-white"
        >


            <!-- LOADING -->

            <div
                v-if="loading"
                class="p-10 text-center
                       text-gray-500"
            >

                Memuat pasien...

            </div>


            <!-- EMPTY -->

            <div
                v-else-if="
                    pasienUntukDiperiksa.length === 0
                "
                class="p-10 text-center"
            >

                <div class="mb-4 text-5xl">
                    🩺
                </div>

                <h3
                    class="font-semibold text-gray-800"
                >
                    Belum ada pasien
                </h3>

                <p
                    class="mt-1 text-sm text-gray-500"
                >
                    Belum ada pasien yang
                    siap diperiksa.
                </p>

                <button
                    @click="router.push('/antrian')"
                    class="mt-5 rounded-lg
                           bg-blue-600
                           px-4 py-2
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-blue-700"
                >

                    Lihat Antrian

                </button>

            </div>


            <!-- TABLE -->

            <div
                v-else
                class="overflow-x-auto"
            >

                <table class="w-full text-sm">

                    <thead
                        class="border-b
                               bg-gray-50"
                    >

                        <tr>

                            <th
                                class="px-5 py-4
                                       text-left
                                       font-semibold
                                       text-gray-500"
                            >
                                Antrean
                            </th>

                            <th
                                class="px-5 py-4
                                       text-left
                                       font-semibold
                                       text-gray-500"
                            >
                                Pasien
                            </th>

                            <th
                                class="px-5 py-4
                                       text-left
                                       font-semibold
                                       text-gray-500"
                            >
                                Poli
                            </th>

                            <th
                                class="px-5 py-4
                                       text-left
                                       font-semibold
                                       text-gray-500"
                            >
                                Dokter
                            </th>

                            <th
                                class="px-5 py-4
                                       text-left
                                       font-semibold
                                       text-gray-500"
                            >
                                Status
                            </th>

                            <th
                                class="px-5 py-4
                                       text-right
                                       font-semibold
                                       text-gray-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="item in pasienUntukDiperiksa"
                            :key="item.id"
                            class="border-b
                                   border-gray-100
                                   hover:bg-gray-50"
                        >

                            <td class="px-5 py-5">

                                <span
                                    class="text-xl
                                           font-bold
                                           text-blue-600"
                                >

                                    {{ item.no_antrian }}

                                </span>

                            </td>


                            <td class="px-5 py-5">

                                <p
                                    class="font-semibold
                                           text-gray-800"
                                >

                                    {{
                                        item.pasien?.nama || '-'
                                    }}

                                </p>

                                <p
                                    class="mt-1
                                           text-xs
                                           text-gray-500"
                                >

                                    RM:
                                    {{
                                        item.pasien?.no_rm || '-'
                                    }}

                                </p>

                            </td>


                            <td class="px-5 py-5">

                                {{
                                    item.poli?.nama || '-'
                                }}

                            </td>


                            <td class="px-5 py-5">

                                {{
                                    item.dokter?.nama || '-'
                                }}

                            </td>


                            <td class="px-5 py-5">

                                <span
                                    class="rounded-full
                                           px-3 py-1
                                           text-xs
                                           font-semibold"
                                    :class="
                                        statusClass(
                                            item.status
                                        )
                                    "
                                >

                                    {{
                                        statusLabel(
                                            item.status
                                        )
                                    }}

                                </span>

                            </td>


                            <td
                                class="px-5 py-5
                                       text-right"
                            >

                                <button
                                    @click="
                                        mulaiPemeriksaan(item)
                                    "
                                    class="rounded-lg
                                           bg-purple-600
                                           px-4 py-2
                                           text-xs
                                           font-semibold
                                           text-white
                                           hover:bg-purple-700"
                                >

                                    🩺

                                    {{
                                        item.status ===
                                        'DIPERIKSA'
                                            ? 'Buka Pemeriksaan'
                                            : 'Periksa'
                                    }}

                                </button>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </template>


    <!-- ===================================================== -->
    <!-- DETAIL PEMERIKSAAN -->
    <!-- ===================================================== -->

    <template v-else>


        <!-- HEADER -->

        <div>

            <button
                @click="kembali"
                class="mb-2 text-sm
                       font-medium
                       text-blue-600
                       hover:text-blue-800"
            >

                ← Kembali ke Pemeriksaan

            </button>


            <h1
                class="text-2xl
                       font-bold
                       text-gray-800"
            >

                Pemeriksaan Pasien

            </h1>

        </div>


        <!-- ERROR -->

        <div
            v-if="error"
            class="rounded-xl
                   border border-red-200
                   bg-red-50
                   p-4
                   text-red-700"
        >

            {{ error }}

        </div>


        <!-- LOADING -->

        <div
            v-if="loading"
            class="rounded-2xl
                   border
                   bg-white
                   p-10
                   text-center
                   text-gray-500"
        >

            Memuat data pasien...

        </div>


        <!-- ================================================= -->
        <!-- DATA PENDAFTARAN -->
        <!-- ================================================= -->

        <template
            v-else-if="pendaftaran"
        >


            <!-- DATA PASIEN -->

            <div
                class="rounded-2xl
                       border border-gray-200
                       bg-white
                       p-6"
            >

                <div
                    class="flex flex-col
                           gap-5
                           md:flex-row
                           md:items-center
                           md:justify-between"
                >


                    <div>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >

                            Pasien

                        </p>


                        <h2
                            class="text-xl
                                   font-bold
                                   text-gray-800"
                        >

                            {{
                                pendaftaran.pasien?.nama ||
                                '-'
                            }}

                        </h2>


                        <p
                            class="mt-1
                                   text-sm
                                   text-gray-500"
                        >

                            No. RM:
                            {{
                                pendaftaran.pasien?.no_rm ||
                                '-'
                            }}

                        </p>

                    </div>


                    <div
                        class="flex flex-wrap gap-3"
                    >


                        <!-- ANTRIAN -->

                        <div
                            class="rounded-xl
                                   bg-blue-50
                                   px-4 py-3"
                        >

                            <p
                                class="text-xs
                                       text-blue-500"
                            >

                                No. Antrean

                            </p>


                            <p
                                class="text-lg
                                       font-bold
                                       text-blue-700"
                            >

                                {{
                                    pendaftaran.no_antrian
                                }}

                            </p>

                        </div>


                        <!-- POLI -->

                        <div
                            class="rounded-xl
                                   bg-purple-50
                                   px-4 py-3"
                        >

                            <p
                                class="text-xs
                                       text-purple-500"
                            >

                                Poli

                            </p>


                            <p
                                class="text-sm
                                       font-bold
                                       text-purple-700"
                            >

                                {{
                                    pendaftaran.poli?.nama ||
                                    '-'
                                }}

                            </p>

                        </div>


                        <!-- DOKTER -->

                        <div
                            class="rounded-xl
                                   bg-gray-50
                                   px-4 py-3"
                        >

                            <p
                                class="text-xs
                                       text-gray-500"
                            >

                                Dokter

                            </p>


                            <p
                                class="text-sm
                                       font-bold
                                       text-gray-700"
                            >

                                {{
                                    pendaftaran.dokter?.nama ||
                                    '-'
                                }}

                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- FORM -->
            <!-- ================================================= -->

            <div
                class="rounded-2xl
                       border border-gray-200
                       bg-white
                       p-6"
            >

                <div class="mb-6">

                    <h2
                        class="text-lg
                               font-bold
                               text-gray-800"
                    >

                        Hasil Pemeriksaan

                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >

                        Masukkan hasil pemeriksaan
                        pasien.

                    </p>

                </div>


                <!-- VITAL SIGN -->

                <div
                    class="grid
                           grid-cols-1
                           gap-5
                           md:grid-cols-2
                           lg:grid-cols-3"
                >


                    <!-- TEKANAN DARAH -->

                    <div>

                        <label class="label">
                            Tekanan Darah
                        </label>

                        <input
                            v-model="form.tekanan_darah"
                            type="text"
                            placeholder="120/80"
                            class="input"
                        >

                    </div>


                    <!-- SUHU -->

                    <div>

                        <label class="label">
                            Suhu (°C)
                        </label>

                        <input
                            v-model="form.suhu"
                            type="number"
                            step="0.1"
                            placeholder="36.5"
                            class="input"
                        >

                    </div>


                    <!-- BERAT -->

                    <div>

                        <label class="label">
                            Berat Badan (kg)
                        </label>

                        <input
                            v-model="form.berat_badan"
                            type="number"
                            step="0.1"
                            placeholder="60"
                            class="input"
                        >

                    </div>


                    <!-- TINGGI -->

                    <div>

                        <label class="label">
                            Tinggi Badan (cm)
                        </label>

                        <input
                            v-model="form.tinggi_badan"
                            type="number"
                            step="0.1"
                            placeholder="170"
                            class="input"
                        >

                    </div>


                    <!-- NADI -->

                    <div>

                        <label class="label">
                            Nadi (bpm)
                        </label>

                        <input
                            v-model="form.nadi"
                            type="number"
                            placeholder="80"
                            class="input"
                        >

                    </div>


                    <!-- RESPIRASI -->

                    <div>

                        <label class="label">
                            Respirasi (x/menit)
                        </label>

                        <input
                            v-model="form.respirasi"
                            type="number"
                            placeholder="20"
                            class="input"
                        >

                    </div>

                </div>


                <!-- DIAGNOSIS -->

                <div class="mt-6">

                    <label class="label">
                        Diagnosis
                    </label>

                    <textarea
                        v-model="form.diagnosis"
                        rows="4"
                        placeholder="Masukkan diagnosis pasien..."
                        class="textarea"
                    ></textarea>

                </div>


                <!-- TINDAKAN -->

                <div class="mt-5">

                    <label class="label">
                        Tindakan
                    </label>

                    <textarea
                        v-model="form.tindakan"
                        rows="4"
                        placeholder="Masukkan tindakan yang dilakukan..."
                        class="textarea"
                    ></textarea>

                </div>


                <!-- CATATAN -->

                <div class="mt-5">

                    <label class="label">
                        Catatan
                    </label>

                    <textarea
                        v-model="form.catatan"
                        rows="4"
                        placeholder="Catatan tambahan..."
                        class="textarea"
                    ></textarea>

                </div>


                <!-- SUCCESS -->

                <div
                    v-if="success"
                    class="mt-5
                           rounded-xl
                           border border-green-200
                           bg-green-50
                           p-4
                           text-green-700"
                >

                    {{ success }}

                </div>


                <!-- BUTTON -->

                <div
                    class="mt-6
                           flex
                           justify-end
                           gap-3"
                >

                    <button
                        @click="kembali"
                        type="button"
                        class="rounded-xl
                               border
                               border-gray-300
                               px-5 py-3
                               text-gray-700
                               hover:bg-gray-50"
                    >

                        Kembali

                    </button>


                    <button
                        @click="simpanPemeriksaan"
                        :disabled="saving"
                        type="button"
                        class="rounded-xl
                               bg-blue-600
                               px-5 py-3
                               font-semibold
                               text-white
                               hover:bg-blue-700
                               disabled:opacity-50"
                    >

                        {{
                            saving
                                ? 'Menyimpan...'
                                : '💾 Simpan Pemeriksaan'
                        }}

                    </button>

                </div>

            </div>

        </template>


        <!-- ================================================= -->
        <!-- DATA BENAR-BENAR TIDAK ADA -->
        <!-- ================================================= -->

        <div
            v-else
            class="rounded-2xl
                   border
                   bg-white
                   p-10
                   text-center"
        >

            <div class="mb-4 text-5xl">
                ⚠️
            </div>


            <h3
                class="font-semibold
                       text-gray-800"
            >

                Data pendaftaran tidak ditemukan

            </h3>


            <p
                class="mt-2 text-sm
                       text-gray-500"
            >

                ID pendaftaran:
                {{ pendaftaranId }}

            </p>


            <button
                @click="kembali"
                class="mt-5
                       rounded-lg
                       bg-blue-600
                       px-4 py-2
                       text-white"
            >

                Kembali

            </button>

        </div>

    </template>

</div>

</template>


<style scoped>

.label {

    display: block;

    margin-bottom: 0.5rem;

    font-size: 0.875rem;

    font-weight: 500;

    color: #374151;

}


.input {

    width: 100%;

    padding: 0.75rem 1rem;

    border: 1px solid #d1d5db;

    border-radius: 0.75rem;

    outline: none;

    transition: all 0.2s ease;

}


.input:focus {

    border-color: #3b82f6;

    box-shadow:
        0 0 0 2px
        rgba(59, 130, 246, 0.15);

}


.textarea {

    width: 100%;

    padding: 0.75rem 1rem;

    border: 1px solid #d1d5db;

    border-radius: 0.75rem;

    outline: none;

    resize: vertical;

    transition: all 0.2s ease;

}


.textarea:focus {

    border-color: #3b82f6;

    box-shadow:
        0 0 0 2px
        rgba(59, 130, 246, 0.15);

}

</style>
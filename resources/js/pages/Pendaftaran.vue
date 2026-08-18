<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const pasiens = ref([]);
const polis = ref([]);
const dokters = ref([]);
const pendaftarans = ref([]);

const pasienId = ref('');
const poliId = ref('');
const dokterId = ref('');
const tanggalKunjungan = ref('');
const keluhan = ref('');

const loading = ref(false);
const loadingData = ref(false);
const error = ref('');
const success = ref('');


/*
|--------------------------------------------------------------------------
| Ambil data form
|--------------------------------------------------------------------------
*/

const getFormData = async () => {
    try {
        loadingData.value = true;
        error.value = '';

        const response = await axios.get(
            '/api/pendaftaran-form'
        );

        console.log(
            'Form data:',
            response.data
        );

        pasiens.value =
            response.data.pasiens || [];

        polis.value =
            response.data.polis || [];

        dokters.value =
            response.data.dokters || [];

    } catch (err) {

        console.error(
            'Error form:',
            err
        );

        console.error(
            'Response:',
            err.response?.data
        );

        error.value =
            err.response?.data?.message ||
            err.message ||
            'Gagal mengambil data form.';

    } finally {

        loadingData.value = false;

    }
};


/*
|--------------------------------------------------------------------------
| Ambil daftar pendaftaran
|--------------------------------------------------------------------------
*/

const getPendaftarans = async () => {

    try {

        const response = await axios.get(
            '/api/pendaftarans'
        );

        console.log(
            'Data pendaftaran:',
            response.data
        );

        pendaftarans.value =
            response.data.pendaftarans || [];

    } catch (err) {

        console.error(
            'Error mengambil pendaftaran:',
            err
        );

        console.error(
            'Response:',
            err.response?.data
        );

    }

};


/*
|--------------------------------------------------------------------------
| Submit Pendaftaran
|--------------------------------------------------------------------------
*/

const submitPendaftaran = async () => {

    error.value = '';
    success.value = '';

    /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

    if (!pasienId.value) {

        error.value =
            'Silakan pilih pasien.';

        return;
    }

    if (!poliId.value) {

        error.value =
            'Silakan pilih poli.';

        return;
    }

    if (!dokterId.value) {

        error.value =
            'Silakan pilih dokter.';

        return;
    }

    if (!tanggalKunjungan.value) {

        error.value =
            'Silakan pilih tanggal kunjungan.';

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Loading
    |--------------------------------------------------------------------------
    */

    loading.value = true;


    try {

        /*
        |--------------------------------------------------------------------------
        | Kirim data ke Laravel
        |--------------------------------------------------------------------------
        */

        const response = await axios.post(
            '/api/pendaftarans',
            {
                pasien_id: pasienId.value,
                poli_id: poliId.value,
                dokter_id: dokterId.value,
                tanggal_kunjungan:
                    tanggalKunjungan.value,
                keluhan:
                    keluhan.value || null,
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Debug response
        |--------------------------------------------------------------------------
        */

        console.log(
            '================================'
        );

        console.log(
            'Pendaftaran berhasil'
        );

        console.log(
            'Response:',
            response
        );

        console.log(
            'Response data:',
            response.data
        );

        console.log(
            'Pendaftaran:',
            response.data?.pendaftaran
        );

        console.log(
            '================================'
        );


        /*
        |--------------------------------------------------------------------------
        | Ambil data pendaftaran
        |--------------------------------------------------------------------------
        */

        const pendaftaran =
            response.data?.pendaftaran;


        /*
        |--------------------------------------------------------------------------
        | Pastikan backend mengirim data
        |--------------------------------------------------------------------------
        */

        if (!pendaftaran) {

            error.value =
                response.data?.message ||
                'Pendaftaran berhasil tetapi data antrean tidak diterima dari server.';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        success.value =
            `Pendaftaran berhasil. Nomor antrean: ${pendaftaran.no_antrian}`;


        /*
        |--------------------------------------------------------------------------
        | Reset form
        |--------------------------------------------------------------------------
        */

        pasienId.value = '';
        poliId.value = '';
        dokterId.value = '';
        keluhan.value = '';


        /*
        |--------------------------------------------------------------------------
        | Tanggal kembali ke hari ini
        |--------------------------------------------------------------------------
        */

        tanggalKunjungan.value =
            new Date()
                .toISOString()
                .split('T')[0];


        /*
        |--------------------------------------------------------------------------
        | Refresh daftar antrean
        |--------------------------------------------------------------------------
        */

        await getPendaftarans();

    } catch (err) {

        console.error(
            '================================'
        );

        console.error(
            'ERROR PENDAFTARAN'
        );

        console.error(
            'Error object:',
            err
        );

        console.error(
            'Error message:',
            err.message
        );

        console.error(
            'Error response:',
            err.response
        );

        console.error(
            'Error response data:',
            err.response?.data
        );

        console.error(
            '================================'
        );


        /*
        |--------------------------------------------------------------------------
        | Error dari Laravel
        |--------------------------------------------------------------------------
        */

        if (err.response?.data?.message) {

            error.value =
                err.response.data.message;

        }
        /*
        |--------------------------------------------------------------------------
        | Validation Laravel
        |--------------------------------------------------------------------------
        */

        else if (
            err.response?.data?.errors
        ) {

            const errors =
                err.response.data.errors;

            error.value =
                Object.values(errors)
                    .flat()
                    .join(' ');

        }
        /*
        |--------------------------------------------------------------------------
        | Error lainnya
        |--------------------------------------------------------------------------
        */

        else if (err.message) {

            error.value =
                err.message;

        }
        else {

            error.value =
                'Gagal membuat pendaftaran.';

        }

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Filter dokter berdasarkan poli
|--------------------------------------------------------------------------
*/

const filteredDokters = () => {

    if (!poliId.value) {
        return [];
    }

    return dokters.value.filter(
        dokter =>
            String(dokter.poli_id) ===
            String(poliId.value)
    );

};


/*
|--------------------------------------------------------------------------
| Jika poli berubah
|--------------------------------------------------------------------------
*/

watch(
    poliId,
    () => {

        dokterId.value = '';

    }
);


/*
|--------------------------------------------------------------------------
| Saat halaman dibuka
|--------------------------------------------------------------------------
*/

onMounted(() => {

    /*
    |--------------------------------------------------------------------------
    | Default tanggal hari ini
    |--------------------------------------------------------------------------
    */

    const today =
        new Date()
            .toISOString()
            .split('T')[0];

    tanggalKunjungan.value =
        today;


    /*
    |--------------------------------------------------------------------------
    | Load data
    |--------------------------------------------------------------------------
    */

    getFormData();

    getPendaftarans();

});
</script>


<template>

<div class="min-h-screen bg-slate-100">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200">

        <div
            class="h-16 px-6
                   flex items-center justify-between"
        >

            <div class="flex items-center gap-3">

                <div
                    class="w-9 h-9 rounded-lg
                           bg-blue-600
                           flex items-center justify-center
                           text-white font-bold"
                >
                    S
                </div>

                <div>

                    <h1 class="font-bold text-slate-800">
                        SIMRS
                    </h1>

                    <p class="text-xs text-slate-500">
                        Hospital Management System
                    </p>

                </div>

            </div>


            <button
                @click="router.push('/dashboard')"
                class="text-sm text-slate-600
                       hover:text-blue-600"
            >
                ← Dashboard
            </button>

        </div>

    </header>


    <main class="p-6">

        <!-- Header -->
        <div class="mb-6">

            <h2
                class="text-2xl font-bold text-slate-800"
            >
                Pendaftaran Pasien
            </h2>

            <p class="text-slate-500 mt-1">
                Daftarkan pasien untuk mendapatkan nomor antrean.
            </p>

        </div>


        <!-- Error -->
        <div
            v-if="error"
            class="mb-5 p-4 rounded-xl
                   bg-red-50 border border-red-200
                   text-red-700"
        >
            {{ error }}
        </div>


        <!-- Success -->
        <div
            v-if="success"
            class="mb-5 p-5 rounded-xl
                   bg-green-50 border border-green-200
                   text-green-700 font-semibold"
        >
            {{ success }}
        </div>


        <div
            class="grid grid-cols-1
                   xl:grid-cols-3 gap-6"
        >

            <!-- Form -->
            <div
                class="xl:col-span-1
                       bg-white rounded-2xl
                       border border-slate-200
                       p-6"
            >

                <h3
                    class="text-lg font-semibold
                           text-slate-800 mb-5"
                >
                    Form Pendaftaran
                </h3>


                <!-- Pasien -->
                <div class="mb-4">

                    <label
                        class="block text-sm
                               font-medium
                               text-slate-700 mb-2"
                    >
                        Pasien
                    </label>

                    <select
                        v-model="pasienId"
                        class="w-full px-4 py-3
                               rounded-xl
                               border border-slate-300
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500"
                    >

                        <option value="">
                            Pilih Pasien
                        </option>

                        <option
                            v-for="pasien in pasiens"
                            :key="pasien.id"
                            :value="pasien.id"
                        >
                            {{ pasien.no_rm }} -
                            {{ pasien.nama }}
                        </option>

                    </select>

                </div>


                <!-- Poli -->
                <div class="mb-4">

                    <label
                        class="block text-sm
                               font-medium
                               text-slate-700 mb-2"
                    >
                        Poli
                    </label>

                    <select
                        v-model="poliId"
                        class="w-full px-4 py-3
                               rounded-xl
                               border border-slate-300
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500"
                    >

                        <option value="">
                            Pilih Poli
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


                <!-- Dokter -->
                <div class="mb-4">

                    <label
                        class="block text-sm
                               font-medium
                               text-slate-700 mb-2"
                    >
                        Dokter
                    </label>

                    <select
                        v-model="dokterId"
                        :disabled="!poliId"
                        class="w-full px-4 py-3
                               rounded-xl
                               border border-slate-300
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500
                               disabled:bg-slate-100"
                    >

                        <option value="">
                            Pilih Dokter
                        </option>

                        <option
                            v-for="dokter in filteredDokters()"
                            :key="dokter.id"
                            :value="dokter.id"
                        >
                            {{ dokter.nama }}
                            <span
                                v-if="dokter.spesialisasi"
                            >
                                - {{ dokter.spesialisasi }}
                            </span>
                        </option>

                    </select>

                </div>


                <!-- Tanggal -->
                <div class="mb-4">

                    <label
                        class="block text-sm
                               font-medium
                               text-slate-700 mb-2"
                    >
                        Tanggal Kunjungan
                    </label>

                    <input
                        v-model="tanggalKunjungan"
                        type="date"
                        class="w-full px-4 py-3
                               rounded-xl
                               border border-slate-300
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500"
                    >

                </div>


                <!-- Keluhan -->
                <div class="mb-5">

                    <label
                        class="block text-sm
                               font-medium
                               text-slate-700 mb-2"
                    >
                        Keluhan
                    </label>

                    <textarea
                        v-model="keluhan"
                        rows="4"
                        placeholder="Keluhan pasien..."
                        class="w-full px-4 py-3
                               rounded-xl
                               border border-slate-300
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500"
                    ></textarea>

                </div>


                <button
                    @click="submitPendaftaran"
                    :disabled="loading"
                    class="w-full py-3
                           rounded-xl
                           bg-blue-600
                           text-white
                           font-semibold
                           hover:bg-blue-700
                           disabled:opacity-50
                           transition"
                >

                    {{
                        loading
                            ? 'Mendaftarkan...'
                            : 'Daftar Pasien'
                    }}

                </button>

            </div>


            <!-- Queue -->
            <div
                class="xl:col-span-2
                       bg-white rounded-2xl
                       border border-slate-200
                       overflow-hidden"
            >

                <div
                    class="p-6 border-b
                           border-slate-200"
                >

                    <h3
                        class="text-lg font-semibold
                               text-slate-800"
                    >
                        Antrian Pasien
                    </h3>

                    <p
                        class="text-sm
                               text-slate-500 mt-1"
                    >
                        Daftar pasien yang telah melakukan pendaftaran.
                    </p>

                </div>


                <div
                    v-if="pendaftarans.length === 0"
                    class="p-10 text-center
                           text-slate-500"
                >
                    Belum ada pasien yang terdaftar.
                </div>


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
                                           text-slate-500"
                                >
                                    Antrian
                                </th>

                                <th
                                    class="text-left
                                           px-5 py-4
                                           text-slate-500"
                                >
                                    Pasien
                                </th>

                                <th
                                    class="text-left
                                           px-5 py-4
                                           text-slate-500"
                                >
                                    Poli
                                </th>

                                <th
                                    class="text-left
                                           px-5 py-4
                                           text-slate-500"
                                >
                                    Dokter
                                </th>

                                <th
                                    class="text-left
                                           px-5 py-4
                                           text-slate-500"
                                >
                                    Status
                                </th>

                            </tr>

                        </thead>


                        ```html
<tbody>

    <tr
        v-for="pendaftaran in pendaftarans"
        :key="pendaftaran.id"
        class="border-t border-slate-100"
    >

        <!-- Antrean -->
        <td
            class="px-5 py-4
                   font-bold
                   text-blue-600"
        >
            {{ pendaftaran.no_antrian }}
        </td>


        <!-- Pasien -->
        <td class="px-5 py-4">

            <p
                class="font-semibold
                       text-slate-800"
            >
                {{ pendaftaran.pasien?.nama }}
            </p>

            <p
                class="text-xs
                       text-slate-500"
            >
                {{ pendaftaran.pasien?.no_rm }}
            </p>

        </td>


        <!-- Poli -->
        <td class="px-5 py-4">
            {{ pendaftaran.poli?.nama }}
        </td>


        <!-- Dokter -->
        <td class="px-5 py-4">
            {{ pendaftaran.dokter?.nama }}
        </td>


        <!-- Status -->
        <td class="px-5 py-4">

            <span
                class="px-3 py-1
                       rounded-full
                       bg-yellow-100
                       text-yellow-700
                       text-xs
                       font-medium"
            >
                {{ pendaftaran.status }}
            </span>

        </td>


        <!-- Aksi -->
        <td
            class="px-5 py-4
                   text-right"
        >

            <button
                v-if="pendaftaran.pemeriksaan"
                @click="
                    router.push(
                        `/pasien/${pendaftaran.pasien_id}/riwayat-pemeriksaan`
                    )
                "
                class="px-3 py-2
                       rounded-lg
                       bg-green-50
                       text-green-600
                       text-xs
                       font-semibold
                       hover:bg-green-100
                       transition"
            >
                📋 Riwayat
            </button>

        </td>

    </tr>

</tbody>
```


                    </table>

                </div>

            </div>

        </div>

    </main>

</div>

</template>
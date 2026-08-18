<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();


// =================================================
// FORM
// =================================================

const form = ref({
    kode: '',
    prefix: '',
    nama: '',
    deskripsi: '',
    is_active: true,
});


// =================================================
// STATE
// =================================================

const loading = ref(true);
const saving = ref(false);
const error = ref('');


// =================================================
// GET DATA POLI
// =================================================

const getPoli = async () => {

    try {

        const response = await axios.get(
            `/api/polis/${route.params.id}`
        );

        const poli = response.data.poli;


        form.value = {

            kode: poli.kode || '',

            // PENTING: prefix harus ikut diambil
            prefix: poli.prefix || '',

            nama: poli.nama || '',

            deskripsi: poli.deskripsi || '',

            is_active: Boolean(poli.is_active),

        };


    } catch (err) {

        console.error(err);

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil data poli.';

    } finally {

        loading.value = false;

    }
};


// =================================================
// UPDATE POLI
// =================================================

const updatePoli = async () => {

    error.value = '';


    // Validasi
    if (
        !form.value.kode ||
        !form.value.prefix ||
        !form.value.nama
    ) {

        error.value =
            'Kode, prefix, dan nama poli wajib diisi.';

        return;

    }


    try {

        saving.value = true;


        await axios.put(
            `/api/polis/${route.params.id}`,
            form.value
        );


        // Kembali ke Master Poli
        router.push('/polis');


    } catch (err) {

        console.error(err);


        // Validation Error Laravel
        if (err.response?.status === 422) {

            const errors =
                err.response.data.errors;

            error.value =
                Object.values(errors)
                    .flat()
                    .join(' ');

        } else {

            error.value =
                err.response?.data?.message ||
                'Gagal memperbarui poli.';

        }

    } finally {

        saving.value = false;

    }

};


// =================================================
// CANCEL
// =================================================

const cancel = () => {

    router.push('/polis');

};


// =================================================
// ON MOUNTED
// =================================================

onMounted(() => {

    getPoli();

});
</script>


<template>

    <div class="min-h-screen bg-slate-100">

        <!-- Navbar -->
        <header class="bg-white border-b border-slate-200">

            <div class="h-16 px-6 flex items-center justify-between">

                <div class="flex items-center gap-3">

                    <div
                        class="w-9 h-9 rounded-lg bg-blue-600
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
                    @click="cancel"
                    class="text-sm text-slate-600
                           hover:text-blue-600"
                >
                    ← Master Poli
                </button>

            </div>

        </header>


        <!-- Content -->
        <main class="p-6">

            <div class="max-w-2xl mx-auto">

                <div class="mb-6">

                    <h2 class="text-2xl font-bold text-slate-800">
                        Edit Poli
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Perbarui informasi poli.
                    </p>

                </div>


                <!-- Loading -->
                <div
                    v-if="loading"
                    class="bg-white rounded-2xl
                           border border-slate-200
                           p-8 text-center"
                >
                    <p class="text-slate-500">
                        Memuat data poli...
                    </p>
                </div>


                <!-- Form -->
                <div
                    v-else
                    class="bg-white rounded-2xl
                           border border-slate-200
                           p-6"
                >

                    <!-- Error -->
                    <div
                        v-if="error"
                        class="mb-5 p-4 rounded-xl
                               bg-red-50 border border-red-200
                               text-red-700 text-sm"
                    >
                        {{ error }}
                    </div>


                    <!-- Kode -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Kode Poli
                        </label>
                        <!-- Prefix Antrean -->
<div class="mb-5">

    <label
        class="block text-sm font-medium
               text-slate-700 mb-2"
    >
        Prefix Antrean
    </label>

    <input
        v-model="form.prefix"
        type="text"
        maxlength="5"
        placeholder="Contoh: PG"
        class="w-full px-4 py-3 rounded-xl
               border border-slate-300
               focus:outline-none
               focus:ring-2 focus:ring-blue-500
               uppercase"
    >

    <p class="text-xs text-slate-500 mt-1">
        Digunakan untuk nomor antrean, contoh: PG-001.
    </p>

</div>

                        <input
                            v-model="form.kode"
                            type="text"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Nama -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Nama Poli
                        </label>

                        <input
                            v-model="form.nama"
                            type="text"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Deskripsi -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            v-model="form.deskripsi"
                            rows="4"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        ></textarea>

                    </div>


                    <!-- Status -->
                    <div class="mb-6">

                        <label
                            class="flex items-center gap-3
                                   cursor-pointer"
                        >

                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="w-5 h-5 rounded
                                       border-slate-300
                                       text-blue-600
                                       focus:ring-blue-500"
                            >

                            <div>

                                <p class="text-sm font-medium text-slate-700">
                                    Poli Aktif
                                </p>

                                <p class="text-xs text-slate-500">
                                    Poli dapat digunakan untuk pendaftaran pasien.
                                </p>

                            </div>

                        </label>

                    </div>


                    <!-- Actions -->
                    <div class="flex justify-end gap-3">

                        <button
                            @click="cancel"
                            class="px-5 py-3 rounded-xl
                                   border border-slate-300
                                   text-slate-700
                                   hover:bg-slate-50"
                        >
                            Batal
                        </button>


                        <button
                            @click="updatePoli"
                            :disabled="saving"
                            class="px-5 py-3 rounded-xl
                                   bg-blue-600 text-white
                                   font-semibold
                                   hover:bg-blue-700
                                   disabled:opacity-50"
                        >
                            {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>

                    </div>

                </div>

            </div>

        </main>

    </div>

</template>
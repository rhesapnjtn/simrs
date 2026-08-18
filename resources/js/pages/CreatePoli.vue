```vue
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

                <!-- Header -->
                <div class="mb-6">

                    <h2 class="text-2xl font-bold text-slate-800">
                        Tambah Poli
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Tambahkan poli baru ke dalam sistem rumah sakit.
                    </p>

                </div>


                <!-- Form -->
                <div
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

                        <input
                            v-model="form.kode"
                            type="text"
                            maxlength="20"
                            placeholder="Contoh: POLI-001"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                        <p class="text-xs text-slate-500 mt-2">
                            Kode harus unik.
                        </p>

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
                            maxlength="255"
                            placeholder="Contoh: Poli Umum"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


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
                            maxlength="10"
                            placeholder="Contoh: U, PD, PG"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                        <p class="text-xs text-slate-500 mt-2">
                            Prefix digunakan untuk nomor antrean.
                            Contoh: U-001, PD-001, PG-001.
                        </p>

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
                            placeholder="Deskripsi atau keterangan poli..."
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
                            type="button"
                            class="px-5 py-3 rounded-xl
                                   border border-slate-300
                                   text-slate-700
                                   hover:bg-slate-50"
                        >
                            Batal
                        </button>


                        <button
                            @click="savePoli"
                            type="button"
                            :disabled="loading"
                            class="px-5 py-3 rounded-xl
                                   bg-blue-600 text-white
                                   font-semibold
                                   hover:bg-blue-700
                                   disabled:opacity-50
                                   disabled:cursor-not-allowed"
                        >
                            {{ loading ? 'Menyimpan...' : 'Simpan Poli' }}
                        </button>

                    </div>

                </div>

            </div>

        </main>

    </div>
</template>


<script setup>

import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';


const router = useRouter();


const loading = ref(false);
const error = ref('');


const form = ref({

    kode: '',
    nama: '',
    prefix: '',
    deskripsi: '',
    is_active: true,

});


const savePoli = async () => {

    error.value = '';
    loading.value = true;

    try {

        await axios.post('/api/polis', form.value);

        router.push('/polis');

    } catch (err) {

        console.error(err);

        if (err.response?.status === 422) {

            const errors = err.response.data.errors;

            if (errors) {

                error.value = Object.values(errors)
                    .flat()
                    .join(' ');

            } else {

                error.value =
                    err.response.data.message ||
                    'Data poli tidak valid.';

            }

        } else {

            error.value =
                err.response?.data?.message ||
                'Gagal menambahkan poli.';

        }

    } finally {

        loading.value = false;

    }

};


const cancel = () => {

    router.push('/polis');

};

</script>
```

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const saving = ref(false);
const error = ref('');

const form = ref({
    nik: '',
    nama: '',
    jenis_kelamin: '',
    tanggal_lahir: '',
    alamat: '',
    no_telepon: '',
    golongan_darah: '',
    kontak_darurat: '',
    no_telepon_darurat: '',
    is_active: true,
});

const savePasien = async () => {

    error.value = '';

    if (
        !form.value.nama ||
        !form.value.jenis_kelamin ||
        !form.value.tanggal_lahir
    ) {
        error.value =
            'Nama, jenis kelamin, dan tanggal lahir wajib diisi.';

        return;
    }

    try {

        saving.value = true;

        await axios.post('/api/pasiens', form.value);

        router.push('/pasiens');

    } catch (err) {

        console.error(err);

        if (err.response?.status === 422) {

            const errors = err.response.data.errors;

            error.value = Object.values(errors)
                .flat()
                .join(' ');

        } else {

            error.value =
                err.response?.data?.message ||
                'Gagal menambahkan pasien.';

        }

    } finally {

        saving.value = false;

    }
};

const cancel = () => {
    router.push('/pasiens');
};
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
                    ← Master Pasien
                </button>

            </div>

        </header>


        <!-- Content -->
        <main class="p-6">

            <div class="max-w-3xl mx-auto">

                <!-- Header -->
                <div class="mb-6">

                    <h2 class="text-2xl font-bold text-slate-800">
                        Tambah Pasien
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Daftarkan pasien baru ke sistem rumah sakit.
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


                    <!-- Informasi Pasien -->
                    <div class="mb-6">

                        <h3 class="font-semibold text-slate-800">
                            Informasi Pasien
                        </h3>

                        <p class="text-sm text-slate-500 mt-1">
                            Nomor rekam medis akan dibuat otomatis.
                        </p>

                    </div>


                    <!-- Nama -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Nama Lengkap
                        </label>

                        <input
                            v-model="form.nama"
                            type="text"
                            placeholder="Masukkan nama lengkap"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- NIK -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            NIK
                        </label>

                        <input
                            v-model="form.nik"
                            type="text"
                            maxlength="20"
                            placeholder="Masukkan NIK"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Jenis Kelamin -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Jenis Kelamin
                        </label>

                        <select
                            v-model="form.jenis_kelamin"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   bg-white
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                            <option value="">
                                Pilih jenis kelamin
                            </option>

                            <option value="L">
                                Laki-laki
                            </option>

                            <option value="P">
                                Perempuan
                            </option>

                        </select>

                    </div>


                    <!-- Tanggal Lahir -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Tanggal Lahir
                        </label>

                        <input
                            v-model="form.tanggal_lahir"
                            type="date"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Alamat -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Alamat
                        </label>

                        <textarea
                            v-model="form.alamat"
                            rows="3"
                            placeholder="Masukkan alamat pasien"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        ></textarea>

                    </div>


                    <!-- No Telepon -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            No. Telepon
                        </label>

                        <input
                            v-model="form.no_telepon"
                            type="text"
                            placeholder="08xxxxxxxxxx"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Golongan Darah -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Golongan Darah
                        </label>

                        <select
                            v-model="form.golongan_darah"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   bg-white
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                            <option value="">
                                Pilih golongan darah
                            </option>

                            <option value="A">
                                A
                            </option>

                            <option value="B">
                                B
                            </option>

                            <option value="AB">
                                AB
                            </option>

                            <option value="O">
                                O
                            </option>

                        </select>

                    </div>


                    <!-- Kontak Darurat -->
                    <div class="pt-5 border-t border-slate-200">

                        <h3 class="font-semibold text-slate-800">
                            Kontak Darurat
                        </h3>

                        <p class="text-sm text-slate-500 mt-1 mb-5">
                            Data keluarga atau kontak yang dapat dihubungi.
                        </p>


                        <div class="mb-5">

                            <label
                                class="block text-sm font-medium
                                       text-slate-700 mb-2"
                            >
                                Nama Kontak Darurat
                            </label>

                            <input
                                v-model="form.kontak_darurat"
                                type="text"
                                placeholder="Nama keluarga"
                                class="w-full px-4 py-3 rounded-xl
                                       border border-slate-300
                                       focus:outline-none
                                       focus:ring-2 focus:ring-blue-500"
                            >

                        </div>


                        <div class="mb-6">

                            <label
                                class="block text-sm font-medium
                                       text-slate-700 mb-2"
                            >
                                No. Telepon Darurat
                            </label>

                            <input
                                v-model="form.no_telepon_darurat"
                                type="text"
                                placeholder="08xxxxxxxxxx"
                                class="w-full px-4 py-3 rounded-xl
                                       border border-slate-300
                                       focus:outline-none
                                       focus:ring-2 focus:ring-blue-500"
                            >

                        </div>

                    </div>


                    <!-- Status -->
                    <div class="mb-6">

                        <label
                            class="flex items-center gap-3 cursor-pointer"
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
                                    Pasien Aktif
                                </p>

                                <p class="text-xs text-slate-500">
                                    Pasien dapat melakukan pendaftaran.
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
                            @click="savePasien"
                            :disabled="saving"
                            class="px-5 py-3 rounded-xl
                                   bg-blue-600 text-white
                                   font-semibold
                                   hover:bg-blue-700
                                   disabled:opacity-50"
                        >
                            {{ saving ? 'Menyimpan...' : 'Simpan Pasien' }}
                        </button>

                    </div>

                </div>

            </div>

        </main>

    </div>

</template>
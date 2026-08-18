<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const users = ref([]);
const polis = ref([]);

const loading = ref(true);
const saving = ref(false);
const error = ref('');

const form = ref({
    user_id: '',
    poli_id: '',
    nomor_str: '',
    nama: '',
    spesialisasi: '',
    no_telepon: '',
    is_active: true,
});


const getData = async () => {

    try {

        loading.value = true;
        error.value = '';

        const [usersResponse, polisResponse] = await Promise.all([
            axios.get('/api/dokter-users'),
            axios.get('/api/dokter-polis'),
        ]);

        users.value = usersResponse.data.users;
        polis.value = polisResponse.data.polis;

    } catch (err) {

        console.error(err);

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil data user atau poli.';

    } finally {

        loading.value = false;

    }
};


const selectUser = () => {

    const user = users.value.find(
        user => user.id == form.value.user_id
    );

    if (user) {
        form.value.nama = user.name;
    }

};


const saveDokter = async () => {

    error.value = '';

    if (
        !form.value.user_id ||
        !form.value.poli_id ||
        !form.value.nama
    ) {

        error.value =
            'User dokter, poli, dan nama dokter wajib diisi.';

        return;
    }


    try {

        saving.value = true;

        await axios.post('/api/dokters', form.value);

        router.push('/dokters');

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
                'Gagal menyimpan dokter.';

        }

    } finally {

        saving.value = false;

    }
};


const cancel = () => {
    router.push('/dokters');
};


onMounted(() => {
    getData();
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
                    ← Master Dokter
                </button>

            </div>

        </header>


        <!-- Content -->
        <main class="p-6">

            <div class="max-w-2xl mx-auto">

                <!-- Header -->
                <div class="mb-6">

                    <h2 class="text-2xl font-bold text-slate-800">
                        Tambah Dokter
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Tambahkan dokter dan hubungkan dengan poli.
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
                        Memuat data...
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


                    <!-- User Dokter -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            User Dokter
                        </label>

                        <select
                            v-model="form.user_id"
                            @change="selectUser"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   bg-white
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                            <option value="">
                                Pilih User Dokter
                            </option>

                            <option
                                v-for="user in users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }} — {{ user.email }}
                            </option>

                        </select>

                        <p
                            v-if="users.length === 0"
                            class="text-xs text-orange-600 mt-2"
                        >
                            Tidak ada user dengan role DOKTER
                            yang tersedia.
                        </p>

                    </div>


                    <!-- Poli -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Poli
                        </label>

                        <select
                            v-model="form.poli_id"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   bg-white
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                            <option value="">
                                Pilih Poli
                            </option>

                            <option
                                v-for="poli in polis"
                                :key="poli.id"
                                :value="poli.id"
                            >
                                {{ poli.kode }} — {{ poli.nama }}
                            </option>

                        </select>

                        <p
                            v-if="polis.length === 0"
                            class="text-xs text-orange-600 mt-2"
                        >
                            Belum ada poli aktif.
                        </p>

                    </div>


                    <!-- Nama -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Nama Dokter
                        </label>

                        <input
                            v-model="form.nama"
                            type="text"
                            placeholder="Nama dokter"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- STR -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Nomor STR
                        </label>

                        <input
                            v-model="form.nomor_str"
                            type="text"
                            placeholder="Nomor Surat Tanda Registrasi"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Spesialisasi -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Spesialisasi
                        </label>

                        <input
                            v-model="form.spesialisasi"
                            type="text"
                            placeholder="Contoh: Penyakit Dalam"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Telepon -->
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
                                    Dokter Aktif
                                </p>

                                <p class="text-xs text-slate-500">
                                    Dokter dapat melayani pasien.
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
                            @click="saveDokter"
                            :disabled="saving"
                            class="px-5 py-3 rounded-xl
                                   bg-blue-600 text-white
                                   font-semibold
                                   hover:bg-blue-700
                                   disabled:opacity-50"
                        >
                            {{ saving ? 'Menyimpan...' : 'Simpan Dokter' }}
                        </button>

                    </div>

                </div>

            </div>

        </main>

    </div>

</template>
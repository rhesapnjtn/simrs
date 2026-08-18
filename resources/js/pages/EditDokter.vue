<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const dokterId = route.params.id;

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

        const [
            dokterResponse,
            usersResponse,
            polisResponse
        ] = await Promise.all([

            axios.get(`/api/dokters/${dokterId}`),

            axios.get('/api/dokter-users'),

            axios.get('/api/dokter-polis'),

        ]);


        const dokter = dokterResponse.data.dokter;


        form.value = {
            user_id: dokter.user_id ?? '',
            poli_id: dokter.poli_id ?? '',
            nomor_str: dokter.nomor_str ?? '',
            nama: dokter.nama ?? '',
            spesialisasi: dokter.spesialisasi ?? '',
            no_telepon: dokter.no_telepon ?? '',
            is_active: Boolean(dokter.is_active),
        };


        users.value = usersResponse.data.users;

        /*
         * User yang sedang digunakan dokter
         * harus tetap muncul di dropdown.
         */
        if (
            dokter.user &&
            !users.value.some(user => user.id === dokter.user.id)
        ) {
            users.value.unshift(dokter.user);
        }


        polis.value = polisResponse.data.polis;

    } catch (err) {

        console.error(err);

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil data dokter.';

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


const updateDokter = async () => {

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

        await axios.put(
            `/api/dokters/${dokterId}`,
            form.value
        );

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
                'Gagal memperbarui dokter.';

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
                        Edit Dokter
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Perbarui data dokter rumah sakit.
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
                        Memuat data dokter...
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
                            @click="updateDokter"
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
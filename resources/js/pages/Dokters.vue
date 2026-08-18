<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const dokters = ref([]);
const loading = ref(true);
const error = ref('');

const getDokters = async () => {
    try {
        loading.value = true;
        error.value = '';

        const response = await axios.get('/api/dokters');

        dokters.value = response.data.dokters;

    } catch (err) {

        console.error(err);

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil data dokter.';

    } finally {

        loading.value = false;

    }
};


const goToCreate = () => {
    router.push('/dokters/create');
};


const goToEdit = (dokter) => {
    router.push(`/dokters/${dokter.id}/edit`);
};


const deleteDokter = async (dokter) => {

    const confirmed = confirm(
        `Apakah kamu yakin ingin menghapus ${dokter.nama}?`
    );

    if (!confirmed) {
        return;
    }

    try {

        await axios.delete(`/api/dokters/${dokter.id}`);

        await getDokters();

    } catch (err) {

        console.error(err);

        alert(
            err.response?.data?.message ||
            'Gagal menghapus dokter.'
        );

    }
};


onMounted(() => {
    getDokters();
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
                    @click="router.push('/dashboard')"
                    class="text-sm text-slate-600
                           hover:text-blue-600"
                >
                    ← Dashboard
                </button>

            </div>

        </header>


        <!-- Content -->
        <main class="p-6">

            <!-- Header -->
            <div
                class="flex flex-col md:flex-row
                       md:items-center md:justify-between
                       gap-4 mb-6"
            >

                <div>

                    <h2 class="text-2xl font-bold text-slate-800">
                        Master Dokter
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Kelola data dokter dan poli rumah sakit.
                    </p>

                </div>


                <button
                    @click="goToCreate"
                    class="px-5 py-3 rounded-xl
                           bg-blue-600 text-white
                           font-semibold
                           hover:bg-blue-700
                           transition"
                >
                    + Tambah Dokter
                </button>

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


            <!-- Table -->
            <div
                v-else
                class="bg-white rounded-2xl
                       border border-slate-200
                       overflow-hidden"
            >

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-slate-50">

                            <tr>

                                <th
                                    class="text-left px-6 py-4
                                           font-semibold text-slate-500"
                                >
                                    #
                                </th>

                                <th
                                    class="text-left px-6 py-4
                                           font-semibold text-slate-500"
                                >
                                    Dokter
                                </th>

                                <th
                                    class="text-left px-6 py-4
                                           font-semibold text-slate-500"
                                >
                                    Poli
                                </th>

                                <th
                                    class="text-left px-6 py-4
                                           font-semibold text-slate-500"
                                >
                                    Spesialisasi
                                </th>

                                <th
                                    class="text-left px-6 py-4
                                           font-semibold text-slate-500"
                                >
                                    STR
                                </th>

                                <th
                                    class="text-left px-6 py-4
                                           font-semibold text-slate-500"
                                >
                                    Status
                                </th>

                                <th
                                    class="text-right px-6 py-4
                                           font-semibold text-slate-500"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-for="(dokter, index) in dokters"
                                :key="dokter.id"
                                class="border-t border-slate-100
                                       hover:bg-slate-50"
                            >

                                <!-- No -->
                                <td class="px-6 py-4">
                                    {{ index + 1 }}
                                </td>


                                <!-- Dokter -->
                                <td class="px-6 py-4">

                                    <div>

                                        <p
                                            class="font-semibold
                                                   text-slate-800"
                                        >
                                            {{ dokter.nama }}
                                        </p>

                                        <p
                                            v-if="dokter.user"
                                            class="text-xs
                                                   text-slate-500 mt-1"
                                        >
                                            {{ dokter.user.email }}
                                        </p>

                                    </div>

                                </td>


                                <!-- Poli -->
                                <td class="px-6 py-4">

                                    <span
                                        v-if="dokter.poli"
                                        class="px-3 py-1 rounded-full
                                               bg-blue-100
                                               text-blue-700
                                               text-xs font-medium"
                                    >
                                        {{ dokter.poli.nama }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-slate-400"
                                    >
                                        -
                                    </span>

                                </td>


                                <!-- Spesialisasi -->
                                <td class="px-6 py-4 text-slate-600">
                                    {{ dokter.spesialisasi || '-' }}
                                </td>


                                <!-- STR -->
                                <td class="px-6 py-4 text-slate-600">
                                    {{ dokter.nomor_str || '-' }}
                                </td>


                                <!-- Status -->
                                <td class="px-6 py-4">

                                    <span
                                        v-if="dokter.is_active"
                                        class="px-3 py-1 rounded-full
                                               bg-green-100
                                               text-green-700
                                               text-xs font-medium"
                                    >
                                        Aktif
                                    </span>

                                    <span
                                        v-else
                                        class="px-3 py-1 rounded-full
                                               bg-slate-100
                                               text-slate-600
                                               text-xs font-medium"
                                    >
                                        Tidak Aktif
                                    </span>

                                </td>


                                <!-- Aksi -->
                                <td class="px-6 py-4 text-right">

                                    <button
                                        @click="goToEdit(dokter)"
                                        class="px-3 py-2 rounded-lg
                                               text-blue-600
                                               hover:bg-blue-50"
                                    >
                                        Edit
                                    </button>


                                    <button
                                        @click="deleteDokter(dokter)"
                                        class="px-3 py-2 rounded-lg
                                               text-red-600
                                               hover:bg-red-50 ml-2"
                                    >
                                        Hapus
                                    </button>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr v-if="dokters.length === 0">

                                <td
                                    colspan="7"
                                    class="px-6 py-10
                                           text-center
                                           text-slate-500"
                                >
                                    Belum ada data dokter.
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </main>

    </div>

</template>
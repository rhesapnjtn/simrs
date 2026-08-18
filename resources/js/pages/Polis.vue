<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const polis = ref([]);
const loading = ref(true);
const error = ref('');

const getPolis = async () => {
    try {
        loading.value = true;
        error.value = '';

        const response = await axios.get('/api/polis');

        polis.value = response.data.polis;

    } catch (err) {
        console.error(err);

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil data poli.';

    } finally {
        loading.value = false;
    }
};


const goToCreate = () => {
    router.push('/polis/create');
};


const goToEdit = (poli) => {
    router.push(`/polis/${poli.id}/edit`);
};


const deletePoli = async (poli) => {

    const confirmed = confirm(
        `Apakah kamu yakin ingin menghapus ${poli.nama}?`
    );

    if (!confirmed) {
        return;
    }

    try {

        await axios.delete(`/api/polis/${poli.id}`);

        await getPolis();

    } catch (err) {

        console.error(err);

        alert(
            err.response?.data?.message ||
            'Gagal menghapus poli.'
        );

    }
};


onMounted(() => {
    getPolis();
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
                        Master Poli
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Kelola data poli rumah sakit.
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
                    + Tambah Poli
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
                    Memuat data poli...
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
                                    Kode
                                </th>

                                <th
                                    class="text-left px-6 py-4
                                           font-semibold text-slate-500"
                                >
                                    Nama Poli
                                </th>

                                <th
                                    class="text-left px-6 py-4
                                           font-semibold text-slate-500"
                                >
                                    Deskripsi
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
                                v-for="(poli, index) in polis"
                                :key="poli.id"
                                class="border-t border-slate-100
                                       hover:bg-slate-50"
                            >

                                <td class="px-6 py-4">
                                    {{ index + 1 }}
                                </td>


                                <td class="px-6 py-4">

                                    <span
                                        class="font-semibold
                                               text-slate-800"
                                    >
                                        {{ poli.kode }}
                                    </span>

                                </td>


                                <td class="px-6 py-4">

                                    <p
                                        class="font-semibold
                                               text-slate-800"
                                    >
                                        {{ poli.nama }}
                                    </p>

                                </td>


                                <td class="px-6 py-4 text-slate-600">

                                    {{ poli.deskripsi || '-' }}

                                </td>


                                <td class="px-6 py-4">

                                    <span
                                        v-if="poli.is_active"
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
                                        Nonaktif
                                    </span>

                                </td>


                                <td class="px-6 py-4 text-right">

                                    <button
                                        @click="goToEdit(poli)"
                                        class="px-3 py-2 rounded-lg
                                               text-blue-600
                                               hover:bg-blue-50"
                                    >
                                        Edit
                                    </button>


                                    <button
                                        @click="deletePoli(poli)"
                                        class="px-3 py-2 rounded-lg
                                               text-red-600
                                               hover:bg-red-50 ml-2"
                                    >
                                        Hapus
                                    </button>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr v-if="polis.length === 0">

                                <td
                                    colspan="6"
                                    class="px-6 py-10
                                           text-center text-slate-500"
                                >
                                    Belum ada data poli.
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </main>

    </div>

</template>
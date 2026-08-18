```vue
<template>
    <div class="p-6">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Apotek
            </h1>

            <p class="text-gray-500">
                Kelola resep dan proses obat pasien
            </p>
        </div>


        <!-- Filter -->
        <div class="mb-4 flex gap-3">

            <input
                v-model="search"
                type="text"
                placeholder="Cari no. resep atau nama pasien..."
                class="w-full max-w-md rounded-lg border border-gray-300 px-4 py-2
                       focus:border-blue-500 focus:outline-none"
                @input="loadReseps"
            />

            <select
                v-model="status"
                class="rounded-lg border border-gray-300 px-4 py-2
                       focus:border-blue-500 focus:outline-none"
                @change="loadReseps"
            >
                <option value="">Semua Status</option>
                <option value="MENUNGGU">Menunggu</option>
                <option value="DIPROSES">Diproses</option>
                <option value="SELESAI">Selesai</option>
                <option value="BATAL">Batal</option>
            </select>

        </div>


        <!-- Table -->
        <div class="overflow-hidden rounded-xl bg-white shadow">

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-100">

                        <tr>

                            <th class="px-4 py-3 text-left">
                                No. Resep
                            </th>

                            <th class="px-4 py-3 text-left">
                                Pasien
                            </th>

                            <th class="px-4 py-3 text-left">
                                Dokter
                            </th>

                            <th class="px-4 py-3 text-left">
                                Tanggal
                            </th>

                            <th class="px-4 py-3 text-left">
                                Jumlah Obat
                            </th>

                            <th class="px-4 py-3 text-left">
                                Status
                            </th>

                            <th class="px-4 py-3 text-left">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <!-- Loading -->
                        <tr v-if="loading">

                            <td
                                colspan="7"
                                class="px-4 py-8 text-center text-gray-500"
                            >
                                Memuat data...
                            </td>

                        </tr>


                        <!-- Empty -->
                        <tr v-else-if="reseps.length === 0">

                            <td
                                colspan="7"
                                class="px-4 py-8 text-center text-gray-500"
                            >
                                Tidak ada resep.
                            </td>

                        </tr>


                        <!-- Data -->
                        <tr
                            v-for="resep in reseps"
                            :key="resep.id"
                            class="border-t hover:bg-gray-50"
                        >

                            <!-- No Resep -->
                            <td class="px-4 py-3 font-medium">
                                {{ resep.no_resep }}
                            </td>


                            <!-- Pasien -->
                            <td class="px-4 py-3">
                                {{ resep.pendaftaran?.pasien?.nama ?? '-' }}
                            </td>


                            <!-- Dokter -->
                            <td class="px-4 py-3">
                                {{ resep.dokter?.nama ?? '-' }}
                            </td>


                            <!-- Tanggal -->
                            <td class="px-4 py-3">
                                {{ formatTanggal(resep.tanggal_resep) }}
                            </td>


                            <!-- Jumlah Obat -->
                            <td class="px-4 py-3">
                                {{ resep.details?.length ?? 0 }} obat
                            </td>


                            <!-- Status -->
                            <td class="px-4 py-3">

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="statusClass(resep.status)"
                                >
                                    {{ resep.status }}
                                </span>

                            </td>


                            <!-- Aksi -->
                            <td class="px-4 py-3">

                                <div class="flex flex-wrap gap-2">

                                    <!-- Detail -->
                                    <button
                                        @click="lihatDetail(resep)"
                                        class="rounded-lg bg-gray-100 px-3 py-1.5
                                               text-sm hover:bg-gray-200"
                                    >
                                        Detail
                                    </button>


                                    <!-- Proses -->
                                    <button
                                        v-if="resep.status === 'MENUNGGU'"
                                        @click="prosesResep(resep)"
                                        class="rounded-lg bg-blue-600 px-3 py-1.5
                                               text-sm text-white hover:bg-blue-700"
                                    >
                                        Proses
                                    </button>


                                    <!-- Selesaikan -->
                                    <button
                                        v-if="resep.status === 'DIPROSES'"
                                        @click="selesaiResep(resep)"
                                        class="rounded-lg bg-green-600 px-3 py-1.5
                                               text-sm text-white hover:bg-green-700"
                                    >
                                        Selesaikan
                                    </button>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- Modal Detail -->
        <div
            v-if="selectedResep"
            class="fixed inset-0 z-50 flex items-center justify-center
                   bg-black/50 p-4"
            @click.self="selectedResep = null"
        >

            <div
                class="w-full max-w-2xl rounded-xl bg-white p-6
                       max-h-[90vh] overflow-y-auto"
            >

                <!-- Modal Header -->
                <div class="mb-5 flex items-center justify-between">

                    <div>

                        <h2 class="text-xl font-bold">
                            Detail Resep
                        </h2>

                        <p class="text-sm text-gray-500">
                            {{ selectedResep.no_resep }}
                        </p>

                    </div>


                    <button
                        @click="selectedResep = null"
                        class="text-xl text-gray-500 hover:text-gray-800"
                    >
                        ✕
                    </button>

                </div>


                <!-- Informasi Resep -->
                <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <!-- Pasien -->
                    <div>

                        <p class="text-sm text-gray-500">
                            Pasien
                        </p>

                        <p class="font-medium">
                            {{
                                selectedResep.pendaftaran?.pasien?.nama
                                ?? '-'
                            }}
                        </p>

                    </div>


                    <!-- Dokter -->
                    <div>

                        <p class="text-sm text-gray-500">
                            Dokter
                        </p>

                        <p class="font-medium">
                            {{
                                selectedResep.dokter?.nama
                                ?? '-'
                            }}
                        </p>

                    </div>


                    <!-- Tanggal -->
                    <div>

                        <p class="text-sm text-gray-500">
                            Tanggal Resep
                        </p>

                        <p class="font-medium">
                            {{
                                formatTanggal(
                                    selectedResep.tanggal_resep
                                )
                            }}
                        </p>

                    </div>


                    <!-- Status -->
                    <div>

                        <p class="text-sm text-gray-500">
                            Status
                        </p>

                        <span
                            class="inline-block rounded-full px-3 py-1
                                   text-xs font-semibold"
                            :class="
                                statusClass(
                                    selectedResep.status
                                )
                            "
                        >
                            {{ selectedResep.status }}
                        </span>

                    </div>

                </div>


                <!-- Daftar Obat -->
                <h3 class="mb-3 font-semibold">
                    Daftar Obat
                </h3>


                <div class="overflow-hidden rounded-lg border">

                    <div class="overflow-x-auto">

                        <table class="w-full">

                            <thead class="bg-gray-100">

                                <tr>

                                    <th class="px-3 py-2 text-left">
                                        Obat
                                    </th>

                                    <th class="px-3 py-2 text-left">
                                        Jumlah
                                    </th>

                                    <th class="px-3 py-2 text-left">
                                        Dosis
                                    </th>

                                    <th class="px-3 py-2 text-left">
                                        Aturan Pakai
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr
                                    v-for="detail in selectedResep.details"
                                    :key="detail.id"
                                    class="border-t"
                                >

                                    <td class="px-3 py-2">
                                        {{
                                            detail.obat?.nama_obat
                                            ?? '-'
                                        }}
                                    </td>


                                    <td class="px-3 py-2">
                                        {{ detail.jumlah }}
                                    </td>


                                    <td class="px-3 py-2">
                                        {{ detail.dosis ?? '-' }}
                                    </td>


                                    <td class="px-3 py-2">
                                        {{
                                            detail.aturan_pakai
                                            ?? '-'
                                        }}
                                    </td>

                                </tr>


                                <!-- Tidak ada obat -->
                                <tr
                                    v-if="
                                        !selectedResep.details ||
                                        selectedResep.details.length === 0
                                    "
                                >

                                    <td
                                        colspan="4"
                                        class="px-3 py-5 text-center
                                               text-gray-500"
                                    >
                                        Tidak ada detail obat.
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>


                <!-- Tombol Modal -->
                <div class="mt-5 flex justify-end gap-2">

                    <!-- Proses dari modal -->
                    <button
                        v-if="selectedResep.status === 'MENUNGGU'"
                        @click="prosesResep(selectedResep)"
                        class="rounded-lg bg-blue-600 px-4 py-2
                               text-sm font-medium text-white
                               hover:bg-blue-700"
                    >
                        Proses Resep
                    </button>


                    <!-- Selesaikan dari modal -->
                    <button
                        v-if="selectedResep.status === 'DIPROSES'"
                        @click="selesaiResep(selectedResep)"
                        class="rounded-lg bg-green-600 px-4 py-2
                               text-sm font-medium text-white
                               hover:bg-green-700"
                    >
                        Selesaikan Resep
                    </button>


                    <!-- Tutup -->
                    <button
                        @click="selectedResep = null"
                        class="rounded-lg bg-gray-100 px-4 py-2
                               text-sm font-medium text-gray-700
                               hover:bg-gray-200"
                    >
                        Tutup
                    </button>

                </div>

            </div>

        </div>

    </div>
</template>


<script setup>

import { ref, onMounted } from 'vue'
import axios from 'axios'


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const reseps = ref([])

const loading = ref(false)

const search = ref('')

const status = ref('')

const selectedResep = ref(null)


/*
|--------------------------------------------------------------------------
| LOAD RESEP
|--------------------------------------------------------------------------
*/

const loadReseps = async () => {

    loading.value = true

    try {

        const response = await axios.get(
            '/api/apotek/reseps',
            {
                params: {
                    search: search.value,
                    status: status.value
                }
            }
        )

        reseps.value = response.data.data ?? []

    } catch (error) {

        console.error(
            'Gagal mengambil resep:',
            error
        )

        alert(
            error.response?.data?.message ??
            'Gagal mengambil data resep.'
        )

    } finally {

        loading.value = false

    }
}


/*
|--------------------------------------------------------------------------
| DETAIL RESEP
|--------------------------------------------------------------------------
*/

const lihatDetail = async (resep) => {

    try {

        /*
         * Ambil detail terbaru dari server.
         * Ini memastikan data obat dan status
         * selalu yang paling baru.
         */

        const response = await axios.get(
            `/api/apotek/reseps/${resep.id}`
        )

        selectedResep.value = response.data.data

    } catch (error) {

        console.error(
            'Gagal mengambil detail resep:',
            error
        )

        alert(
            error.response?.data?.message ??
            'Gagal mengambil detail resep.'
        )

    }
}


/*
|--------------------------------------------------------------------------
| PROSES RESEP
|--------------------------------------------------------------------------
*/

const prosesResep = async (resep) => {

    const konfirmasi = confirm(
        `Proses resep ${resep.no_resep}?\n\n` +
        'Resep akan berubah menjadi DIPROSES.'
    )

    if (!konfirmasi) {
        return
    }


    try {

        await axios.put(
            `/api/apotek/reseps/${resep.id}/proses`
        )


        alert(
            'Resep berhasil diproses.'
        )


        /*
         * Tutup modal jika sedang terbuka.
         */
        selectedResep.value = null


        /*
         * Ambil data terbaru.
         */
        await loadReseps()

    } catch (error) {

        console.error(
            'Gagal memproses resep:',
            error
        )

        const message =
            error.response?.data?.message ??
            'Gagal memproses resep.'

        alert(message)

    }

}


/*
|--------------------------------------------------------------------------
| SELESAIKAN RESEP
|--------------------------------------------------------------------------
*/

const selesaiResep = async (resep) => {

    const konfirmasi = confirm(
        `Selesaikan resep ${resep.no_resep}?\n\n` +
        'Pastikan obat sudah diserahkan kepada pasien.'
    )

    if (!konfirmasi) {
        return
    }


    try {

        await axios.put(
            `/api/apotek/reseps/${resep.id}/selesai`
        )


        alert(
            'Resep berhasil diselesaikan.'
        )


        /*
         * Tutup modal jika sedang terbuka.
         */
        selectedResep.value = null


        /*
         * Ambil data terbaru dari database.
         */
        await loadReseps()

    } catch (error) {

        console.error(
            'Gagal menyelesaikan resep:',
            error
        )

        const message =
            error.response?.data?.message ??
            'Gagal menyelesaikan resep.'

        alert(message)

    }

}


/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

const formatTanggal = (tanggal) => {

    if (!tanggal) {
        return '-'
    }


    return new Date(tanggal).toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        }
    )

}


/*
|--------------------------------------------------------------------------
| STATUS CLASS
|--------------------------------------------------------------------------
*/

const statusClass = (status) => {

    switch (status) {

        case 'MENUNGGU':

            return 'bg-yellow-100 text-yellow-700'


        case 'DIPROSES':

            return 'bg-blue-100 text-blue-700'


        case 'SELESAI':

            return 'bg-green-100 text-green-700'


        case 'BATAL':

            return 'bg-red-100 text-red-700'


        default:

            return 'bg-gray-100 text-gray-700'

    }

}


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadReseps()

})

</script>
```

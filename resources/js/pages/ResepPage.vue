<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="mx-auto max-w-7xl">

            <!-- HEADER -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Resep Obat
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Kelola resep dan obat pasien
                    </p>
                </div>

                <button
                    @click="openCreateModal"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    + Buat Resep
                </button>
            </div>

            <!-- ALERT SUCCESS -->
            <div
                v-if="successMessage"
                class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
            >
                {{ successMessage }}
            </div>

            <!-- ALERT ERROR -->
            <div
                v-if="errorMessage"
                class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                {{ errorMessage }}
            </div>

            <!-- TABLE -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="font-semibold text-gray-800">
                        Daftar Resep
                    </h2>
                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-left text-sm">

                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
    <tr>
        <th class="px-6 py-4">
            No. Resep
        </th>

        <th class="px-6 py-4">
            Pasien
        </th>

        <th class="px-6 py-4">
            Tanggal
        </th>

        <th class="px-6 py-4">
            Dokter
        </th>

        <th class="px-6 py-4">
            Jumlah Obat
        </th>

        <th class="px-6 py-4">
            Status
        </th>

        <th class="px-6 py-4 text-center">
            Aksi
        </th>
    </tr>
</thead>

                        <tbody class="divide-y divide-gray-100">

    <!-- LOADING -->
    <tr v-if="loading">
        <td
            colspan="7"
            class="px-6 py-10 text-center text-gray-500"
        >
            Memuat data resep...
        </td>
    </tr>

    <!-- EMPTY -->
    <tr v-else-if="reseps.length === 0">
        <td
            colspan="7"
            class="px-6 py-10 text-center text-gray-500"
        >
            Belum ada resep.
        </td>
    </tr>

    <!-- DATA -->
    <tr
        v-for="resep in reseps"
        :key="resep.id"
        class="transition hover:bg-gray-50"
    >

        <!-- NO RESEP -->
        <td class="px-6 py-4 font-semibold text-gray-800">
            {{ resep.no_resep }}
        </td>

        <!-- PASIEN -->
        <td class="px-6 py-4">
            <div class="font-medium text-gray-800">
                {{ getPatientName(resep) }}
            </div>

            <div class="mt-1 text-xs text-gray-500">
                {{ getPatientRM(resep) }}
            </div>
        </td>

        <!-- TANGGAL -->
        <td class="px-6 py-4 text-gray-600">
            {{ formatDate(resep.tanggal_resep) }}
        </td>

        <!-- DOKTER -->
        <td class="px-6 py-4 text-gray-600">
            {{ getDoctorName(resep) }}
        </td>

        <!-- JUMLAH OBAT -->
        <td class="px-6 py-4 text-gray-600">
            {{ resep.details?.length ?? 0 }} obat
        </td>

        <!-- STATUS -->
        <td class="px-6 py-4">
            <span
                class="rounded-full px-3 py-1 text-xs font-semibold"
                :class="statusClass(resep.status)"
            >
                {{ resep.status }}
            </span>
        </td>

        <!-- AKSI -->
        <td class="px-6 py-4 text-center">

            <button
    @click="viewResep(resep)"
    class="mr-2 rounded-lg bg-gray-100 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-200"
>
    Detail
</button>

<button
    v-if="resep.status === 'MENUNGGU'"
    @click="processResep(resep)"
    class="mr-2 rounded-lg bg-blue-100 px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-200"
>
    Proses
</button>

<button
    @click="deleteResep(resep)"
    class="rounded-lg bg-red-100 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-200"
>
    Hapus
</button>

        </td>

    </tr>

</tbody>

                    </table>

                </div>
            </div>

        </div>

        <!-- ================================================= -->
        <!-- MODAL BUAT RESEP -->
        <!-- ================================================= -->

        <div
            v-if="showCreateModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >

            <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-2xl bg-white shadow-xl">

                <!-- MODAL HEADER -->
                <div class="flex items-center justify-between border-b px-6 py-5">

                    <div>
                        <h2 class="text-xl font-bold text-gray-800">
                            Buat Resep Baru
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Masukkan obat yang diresepkan kepada pasien.
                        </p>
                    </div>

                    <button
                        @click="closeCreateModal"
                        class="text-2xl text-gray-400 hover:text-gray-700"
                    >
                        ×
                    </button>

                </div>

                <!-- FORM -->
                <form
                    @submit.prevent="submitResep"
                    class="space-y-6 p-6"
                >

                    <!-- DATA RESEP -->
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <!-- PENDAFTARAN -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Pendaftaran
                            </label>

                            <select
                                v-model="form.pendaftaran_id"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >
                                <option value="">
                                    Pilih pendaftaran
                                </option>

                                <option
                                    v-for="pendaftaran in pendaftarans"
                                    :key="pendaftaran.id"
                                    :value="pendaftaran.id"
                                >
                                    {{ getRegistrationLabel(pendaftaran) }}
                                </option>
                            </select>
                        </div>

                        <!-- DOKTER -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Dokter
                            </label>

                            <select
                                v-model="form.dokter_id"
                                required
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >
                                <option value="">
                                    Pilih dokter
                                </option>

                                <option
                                    v-for="dokter in dokters"
                                    :key="dokter.id"
                                    :value="dokter.id"
                                >
                                    {{ getDoctorNameFromData(dokter) }}
                                </option>
                            </select>
                        </div>

                    </div>

                    <!-- TANGGAL -->
                    <div class="md:w-1/2">
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Tanggal Resep
                        </label>

                        <input
                            v-model="form.tanggal_resep"
                            type="date"
                            required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        />
                    </div>

                    <!-- OBAT -->
                    <div class="rounded-xl border border-gray-200">

                        <div class="flex items-center justify-between border-b bg-gray-50 px-5 py-4">

                            <div>
                                <h3 class="font-semibold text-gray-800">
                                    Obat
                                </h3>

                                <p class="text-xs text-gray-500">
                                    Tambahkan obat ke dalam resep
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="addDetail"
                                class="rounded-lg bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-700"
                            >
                                + Tambah Obat
                            </button>

                        </div>

                        <div class="space-y-5 p-5">

                            <div
                                v-for="(detail, index) in form.details"
                                :key="index"
                                class="rounded-xl border border-gray-200 bg-white p-5"
                            >

                                <div class="mb-4 flex items-center justify-between">

                                    <h4 class="font-semibold text-gray-700">
                                        Obat {{ index + 1 }}
                                    </h4>

                                    <button
                                        v-if="form.details.length > 1"
                                        type="button"
                                        @click="removeDetail(index)"
                                        class="text-sm font-medium text-red-600 hover:text-red-700"
                                    >
                                        Hapus
                                    </button>

                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                    <!-- OBAT -->
                                    <div class="md:col-span-2">

                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                            Nama Obat
                                        </label>

                                        <select
                                            v-model="detail.obat_id"
                                            required
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        >
                                            <option value="">
                                                Pilih obat
                                            </option>

                                            <option
                                                v-for="obat in activeObats"
                                                :key="obat.id"
                                                :value="obat.id"
                                            >
                                                {{ obat.kode_obat }} -
                                                {{ obat.nama_obat }}
                                                (Stok: {{ obat.stok }})
                                            </option>

                                        </select>

                                    </div>

                                    <!-- JUMLAH -->
                                    <div>

                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                            Jumlah
                                        </label>

                                        <input
                                            v-model.number="detail.jumlah"
                                            type="number"
                                            min="1"
                                            required
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        />

                                    </div>

                                    <!-- DOSIS -->
                                    <div>

                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                            Dosis
                                        </label>

                                        <input
                                            v-model="detail.dosis"
                                            type="text"
                                            placeholder="Contoh: 500 mg"
                                            required
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        />

                                    </div>

                                    <!-- ATURAN PAKAI -->
                                    <div>

                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                            Aturan Pakai
                                        </label>

                                        <input
                                            v-model="detail.aturan_pakai"
                                            type="text"
                                            placeholder="Contoh: 3 x sehari"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        />

                                    </div>

                                    <!-- CATATAN -->
                                    <div>

                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                            Catatan
                                        </label>

                                        <input
                                            v-model="detail.catatan"
                                            type="text"
                                            placeholder="Opsional"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        />

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- CATATAN RESEP -->
                    <div>

                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Catatan Resep
                        </label>

                        <textarea
                            v-model="form.catatan"
                            rows="3"
                            placeholder="Catatan tambahan untuk resep..."
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        ></textarea>

                    </div>

                    <!-- BUTTON -->
                    <div class="flex justify-end gap-3 border-t pt-5">

                        <button
                            type="button"
                            @click="closeCreateModal"
                            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            :disabled="submitting"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ submitting ? 'Menyimpan...' : 'Simpan Resep' }}
                        </button>

                    </div>

                </form>

            </div>

        </div>

        <!-- ================================================= -->
        <!-- MODAL DETAIL RESEP -->
        <!-- ================================================= -->

        <div
            v-if="selectedResep"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >

            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-xl">

                <div class="flex items-center justify-between border-b px-6 py-5">

                    <div>
                        <h2 class="text-xl font-bold text-gray-800">
                            Detail Resep
                        </h2>

                        <p class="text-sm text-gray-500">
                            {{ selectedResep.no_resep }}
                        </p>
                    </div>

                    <button
                        @click="selectedResep = null"
                        class="text-2xl text-gray-400 hover:text-gray-700"
                    >
                        ×
                    </button>

                </div>

                <div class="space-y-6 p-6">

                    <!-- INFO -->
                    <div class="grid grid-cols-1 gap-4 rounded-xl bg-gray-50 p-5 md:grid-cols-3">

                        <div>
                            <p class="text-xs text-gray-500">
                                No. Resep
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ selectedResep.no_resep }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">
                                Dokter
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ getDoctorName(selectedResep) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">
                                Status
                            </p>

                            <span
                                class="mt-1 inline-block rounded-full px-3 py-1 text-xs font-semibold"
                                :class="statusClass(selectedResep.status)"
                            >
                                {{ selectedResep.status }}
                            </span>
                        </div>

                    </div>

                    <!-- DETAIL OBAT -->
                    <div>

                        <h3 class="mb-3 font-semibold text-gray-800">
                            Daftar Obat
                        </h3>

                        <div class="overflow-hidden rounded-xl border">

                            <table class="w-full text-left text-sm">

                                <thead class="bg-gray-50 text-xs uppercase text-gray-500">

                                    <tr>
                                        <th class="px-4 py-3">
                                            Obat
                                        </th>

                                        <th class="px-4 py-3">
                                            Jumlah
                                        </th>

                                        <th class="px-4 py-3">
                                            Dosis
                                        </th>

                                        <th class="px-4 py-3">
                                            Aturan Pakai
                                        </th>
                                    </tr>

                                </thead>

                                <tbody class="divide-y">

                                    <tr
                                        v-for="detail in selectedResep.details"
                                        :key="detail.id"
                                    >

                                        <td class="px-4 py-4 font-medium text-gray-800">
                                            {{ detail.obat?.nama_obat ?? '-' }}
                                        </td>

                                        <td class="px-4 py-4 text-gray-600">
                                            {{ detail.jumlah }}
                                        </td>

                                        <td class="px-4 py-4 text-gray-600">
                                            {{ detail.dosis }}
                                        </td>

                                        <td class="px-4 py-4 text-gray-600">
                                            {{ detail.aturan_pakai ?? '-' }}
                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <!-- CATATAN -->
                    <div
                        v-if="selectedResep.catatan"
                        class="rounded-xl bg-yellow-50 p-4"
                    >

                        <p class="text-xs font-semibold uppercase text-yellow-700">
                            Catatan
                        </p>

                        <p class="mt-1 text-sm text-yellow-800">
                            {{ selectedResep.catatan }}
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const reseps = ref([])
const pendaftarans = ref([])
const dokters = ref([])
const obats = ref([])

const loading = ref(false)
const submitting = ref(false)

const successMessage = ref('')
const errorMessage = ref('')

const showCreateModal = ref(false)
const selectedResep = ref(null)

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = ref({
    pendaftaran_id: '',
    dokter_id: '',
    tanggal_resep: getToday(),
    catatan: '',
    details: [
        createEmptyDetail()
    ]
})

/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const activeObats = computed(() => {
    return obats.value.filter(obat => obat.is_active)
})

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function getToday() {
    const date = new Date()

    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')

    return `${year}-${month}-${day}`
}

function createEmptyDetail() {
    return {
        obat_id: '',
        jumlah: 1,
        dosis: '',
        aturan_pakai: '',
        catatan: ''
    }
}

function getDoctorName(resep) {
    if (!resep?.dokter) {
        return '-'
    }

    return (
        resep.dokter.nama_dokter ??
        resep.dokter.nama ??
        resep.dokter.name ??
        '-'
    )
}

function getDoctorNameFromData(dokter) {
    return (
        dokter.nama_dokter ??
        dokter.nama ??
        dokter.name ??
        '-'
    )
}

function getRegistrationLabel(pendaftaran) {
    const pasien =
        pendaftaran.pasien?.nama ??
        pendaftaran.pasien?.nama_pasien ??
        pendaftaran.nama_pasien ??
        `Pendaftaran #${pendaftaran.id}`

    return `#${pendaftaran.id} - ${pasien}`
}

function formatDate(date) {
    if (!date) {
        return '-'
    }

    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

function getPatientName(resep) {
    return (
        resep?.pendaftaran?.pasien?.nama ??
        resep?.pendaftaran?.pasien?.nama_pasien ??
        '-'
    )
}

function getPatientRM(resep) {
    return (
        resep?.pendaftaran?.pasien?.no_rm ??
        '-'
    )
}

function statusClass(status) {
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
| Modal
|--------------------------------------------------------------------------
*/

function openCreateModal() {
    resetForm()

    showCreateModal.value = true

    loadFormData()
}

function closeCreateModal() {
    showCreateModal.value = false
}

/*
|--------------------------------------------------------------------------
| Form Reset
|--------------------------------------------------------------------------
*/

function resetForm() {
    form.value = {
        pendaftaran_id: '',
        dokter_id: '',
        tanggal_resep: getToday(),
        catatan: '',
        details: [
            createEmptyDetail()
        ]
    }

    errorMessage.value = ''
}

/*
|--------------------------------------------------------------------------
| Tambah / Hapus Obat
|--------------------------------------------------------------------------
*/

function addDetail() {
    form.value.details.push(
        createEmptyDetail()
    )
}

function removeDetail(index) {
    if (form.value.details.length <= 1) {
        return
    }

    form.value.details.splice(index, 1)
}

/*
|--------------------------------------------------------------------------
| API - Load Resep
|--------------------------------------------------------------------------
*/

async function loadReseps() {
    loading.value = true

    try {
        const response = await axios.get(
            '/api/reseps',
            {
                withCredentials: true
            }
        )

        reseps.value = response.data.data ?? []

    } catch (error) {

        console.error(error)

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal mengambil data resep.'

    } finally {
        loading.value = false
    }
}

/*
|--------------------------------------------------------------------------
| API - Load Form Data
|--------------------------------------------------------------------------
*/

async function loadFormData() {
    try {

        const [
            pendaftaranResponse,
            dokterResponse,
            obatResponse
        ] = await Promise.all([
            axios.get(
                '/api/pendaftarans',
                {
                    withCredentials: true
                }
            ),

            axios.get(
                '/api/dokters',
                {
                    withCredentials: true
                }
            ),

            axios.get(
                '/api/obats',
                {
                    withCredentials: true
                }
            )
        ])

        pendaftarans.value =
            pendaftaranResponse.data.pendaftarans ??
            []

        dokters.value =
            dokterResponse.data.dokters ??
            []

        obats.value =
            obatResponse.data.obats ??
            []

    } catch (error) {

        console.error(error)

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal mengambil data form.'

    }
}

/*
|--------------------------------------------------------------------------
| API - Submit Resep
|--------------------------------------------------------------------------
*/

async function submitResep() {
    submitting.value = true
    errorMessage.value = ''
    successMessage.value = ''

    try {

        const response = await axios.post(
            '/api/reseps',
            {
                pendaftaran_id: form.value.pendaftaran_id,
                dokter_id: form.value.dokter_id,
                tanggal_resep: form.value.tanggal_resep,
                catatan: form.value.catatan,
                details: form.value.details
            },
            {
                withCredentials: true
            }
        )

        successMessage.value =
            response.data.message ??
            'Resep berhasil dibuat.'

        closeCreateModal()

        await loadReseps()

    } catch (error) {

        console.error(error)

        if (error.response?.data?.errors) {

            const errors =
                error.response.data.errors

            const firstError =
                Object.values(errors)[0]

            errorMessage.value =
                Array.isArray(firstError)
                    ? firstError[0]
                    : 'Data resep tidak valid.'

        } else {

            errorMessage.value =
                error.response?.data?.message ??
                'Gagal membuat resep.'
        }

    } finally {
        submitting.value = false
    }
}

/*
|--------------------------------------------------------------------------
| Detail Resep
|--------------------------------------------------------------------------
*/

async function viewResep(resep) {
    try {

        const response = await axios.get(
            `/api/reseps/${resep.id}`,
            {
                withCredentials: true
            }
        )

        selectedResep.value =
            response.data.data

    } catch (error) {

        console.error(error)

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal mengambil detail resep.'
    }
}

/*
|--------------------------------------------------------------------------
| Proses Resep
|--------------------------------------------------------------------------
*/

async function processResep(resep) {

    if (!confirm(
        `Proses resep ${resep.no_resep}?`
    )) {
        return
    }

    try {

        await axios.put(
            `/api/reseps/${resep.id}/status`,
            {
                status: 'DIPROSES'
            },
            {
                withCredentials: true
            }
        )

        successMessage.value =
            'Resep berhasil diproses.'

        await loadReseps()

    } catch (error) {

        console.error(error)

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal memproses resep.'
    }
}
async function deleteResep(resep) {
    if (!confirm(`Apakah Anda yakin ingin menghapus resep ${resep.no_resep}?`)) {
        return
    }

    try {
        await axios.delete(
            `/api/reseps/${resep.id}`,
            {
                withCredentials: true
            }
        )

        successMessage.value = 'Resep berhasil dihapus.'

        await loadReseps()

    } catch (error) {
        console.error(error)

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal menghapus resep.'
    }
}

/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {
    loadReseps()
})
</script>
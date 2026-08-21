```vue
<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const labPemeriksaans = ref([])

const loading = ref(false)
const submitting = ref(false)

const search = ref('')

const showModal = ref(false)
const isEdit = ref(false)

const selectedLab = ref(null)

const successMessage = ref('')
const errorMessage = ref('')

/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = ref({
    id: null,
    kode: '',
    nama: '',
    kategori: '',
    satuan: '',
    nilai_rujukan: '',
    harga: 0,
    is_active: true,
})

/*
|--------------------------------------------------------------------------
| COMPUTED
|--------------------------------------------------------------------------
*/

const filteredLabPemeriksaans = computed(() => {
    const keyword = search.value.trim().toLowerCase()

    if (!keyword) {
        return labPemeriksaans.value
    }

    return labPemeriksaans.value.filter((item) => {
        return (
            item.kode?.toLowerCase().includes(keyword) ||
            item.nama?.toLowerCase().includes(keyword) ||
            item.kategori?.toLowerCase().includes(keyword)
        )
    })
})

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function resetMessages() {
    successMessage.value = ''
    errorMessage.value = ''
}

function resetForm() {
    form.value = {
        id: null,
        kode: '',
        nama: '',
        kategori: '',
        satuan: '',
        nilai_rujukan: '',
        harga: 0,
        is_active: true,
    }
}

function formatRupiah(value) {
    const number = Number(value || 0)

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(number)
}

function statusClass(status) {
    return status
        ? 'bg-green-100 text-green-700'
        : 'bg-red-100 text-red-700'
}

function showSuccess(message) {
    successMessage.value = message
    errorMessage.value = ''

    setTimeout(() => {
        successMessage.value = ''
    }, 4000)
}

function showError(message) {
    errorMessage.value = message
    successMessage.value = ''
}

/*
|--------------------------------------------------------------------------
| LOAD DATA
|--------------------------------------------------------------------------
*/

async function loadLabPemeriksaans() {
    loading.value = true
    resetMessages()

    try {
        const response = await axios.get(
            '/api/lab-pemeriksaans',
            {
                withCredentials: true,
            }
        )

        labPemeriksaans.value =
            response.data.data ?? []

    } catch (error) {
        console.error(error)

        showError(
            error.response?.data?.message ??
            'Gagal mengambil data pemeriksaan laboratorium.'
        )
    } finally {
        loading.value = false
    }
}

/*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/

function openCreateModal() {
    resetMessages()

    resetForm()

    isEdit.value = false

    showModal.value = true
}

function openEditModal(item) {
    resetMessages()

    isEdit.value = true

    form.value = {
        id: item.id,
        kode: item.kode ?? '',
        nama: item.nama ?? '',
        kategori: item.kategori ?? '',
        satuan: item.satuan ?? '',
        nilai_rujukan: item.nilai_rujukan ?? '',
        harga: Number(item.harga ?? 0),
        is_active: Boolean(item.is_active),
    }

    showModal.value = true
}

function closeModal() {
    if (submitting.value) {
        return
    }

    showModal.value = false

    selectedLab.value = null
}

/*
|--------------------------------------------------------------------------
| SUBMIT
|--------------------------------------------------------------------------
*/

async function submitForm() {
    submitting.value = true
    resetMessages()

    try {
        const payload = {
            kode: form.value.kode,
            nama: form.value.nama,
            kategori: form.value.kategori || null,
            satuan: form.value.satuan || null,
            nilai_rujukan: form.value.nilai_rujukan || null,
            harga: Number(form.value.harga),
            is_active: form.value.is_active,
        }

        if (isEdit.value) {
            await axios.put(
                `/api/lab-pemeriksaans/${form.value.id}`,
                payload,
                {
                    withCredentials: true,
                }
            )

            showSuccess(
                'Pemeriksaan laboratorium berhasil diperbarui.'
            )
        } else {
            await axios.post(
                '/api/lab-pemeriksaans',
                payload,
                {
                    withCredentials: true,
                }
            )

            showSuccess(
                'Pemeriksaan laboratorium berhasil ditambahkan.'
            )
        }

        closeModal()

        await loadLabPemeriksaans()

    } catch (error) {
        console.error(error)

        if (error.response?.data?.errors) {
            const errors =
                error.response.data.errors

            const firstError =
                Object.values(errors)[0]

            showError(
                Array.isArray(firstError)
                    ? firstError[0]
                    : 'Data tidak valid.'
            )
        } else {
            showError(
                error.response?.data?.message ??
                'Gagal menyimpan pemeriksaan laboratorium.'
            )
        }
    } finally {
        submitting.value = false
    }
}

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

async function deleteLab(item) {
    const confirmed = confirm(
        `Apakah Anda yakin ingin menghapus pemeriksaan "${item.nama}"?`
    )

    if (!confirmed) {
        return
    }

    resetMessages()

    try {
        await axios.delete(
            `/api/lab-pemeriksaans/${item.id}`,
            {
                withCredentials: true,
            }
        )

        showSuccess(
            'Pemeriksaan laboratorium berhasil dihapus.'
        )

        await loadLabPemeriksaans()

    } catch (error) {
        console.error(error)

        showError(
            error.response?.data?.message ??
            'Pemeriksaan laboratorium tidak dapat dihapus.'
        )
    }
}

/*
|--------------------------------------------------------------------------
| TOGGLE STATUS
|--------------------------------------------------------------------------
*/

async function toggleStatus(item) {
    const newStatus = !item.is_active

    try {
        await axios.put(
            `/api/lab-pemeriksaans/${item.id}`,
            {
                kode: item.kode,
                nama: item.nama,
                kategori: item.kategori,
                satuan: item.satuan,
                nilai_rujukan: item.nilai_rujukan,
                harga: Number(item.harga),
                is_active: newStatus,
            },
            {
                withCredentials: true,
            }
        )

        item.is_active = newStatus

        showSuccess(
            newStatus
                ? 'Pemeriksaan berhasil diaktifkan.'
                : 'Pemeriksaan berhasil dinonaktifkan.'
        )

    } catch (error) {
        console.error(error)

        showError(
            error.response?.data?.message ??
            'Gagal mengubah status pemeriksaan.'
        )
    }
}

/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(() => {
    loadLabPemeriksaans()
})
</script>


<template>

    <div class="min-h-screen bg-gray-50 p-4 sm:p-6">

        <div class="mx-auto max-w-7xl">

            <!-- ===================================================== -->
            <!-- HEADER -->
            <!-- ===================================================== -->

            <div
                class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <div>

                    <h1 class="text-2xl font-bold text-gray-800">
                        Pemeriksaan Laboratorium
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Kelola master jenis pemeriksaan laboratorium.
                    </p>

                </div>


                <button
                    @click="openCreateModal"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    + Tambah Pemeriksaan
                </button>

            </div>


            <!-- ===================================================== -->
            <!-- ALERT SUCCESS -->
            <!-- ===================================================== -->

            <div
                v-if="successMessage"
                class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
            >
                {{ successMessage }}
            </div>


            <!-- ===================================================== -->
            <!-- ALERT ERROR -->
            <!-- ===================================================== -->

            <div
                v-if="errorMessage"
                class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                {{ errorMessage }}
            </div>


            <!-- ===================================================== -->
            <!-- CARD -->
            <!-- ===================================================== -->

            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            >

                <!-- ================================================= -->
                <!-- TOOLBAR -->
                <!-- ================================================= -->

                <div
                    class="flex flex-col gap-4 border-b border-gray-200 p-5 md:flex-row md:items-center md:justify-between"
                >

                    <div>

                        <h2 class="font-semibold text-gray-800">
                            Daftar Pemeriksaan
                        </h2>

                        <p class="mt-1 text-xs text-gray-500">
                            {{ filteredLabPemeriksaans.length }}
                            pemeriksaan ditemukan
                        </p>

                    </div>


                    <!-- SEARCH -->

                    <div class="relative w-full md:w-80">

                        <span
                            class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                        >
                            🔎
                        </span>

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari kode, nama, kategori..."
                            class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- TABLE -->
                <!-- ================================================= -->

                <div class="overflow-x-auto">

                    <table class="w-full text-left text-sm">

                        <thead
                            class="bg-gray-50 text-xs uppercase text-gray-500"
                        >

                            <tr>

                                <th class="px-5 py-4">
                                    Kode
                                </th>

                                <th class="px-5 py-4">
                                    Pemeriksaan
                                </th>

                                <th class="px-5 py-4">
                                    Kategori
                                </th>

                                <th class="px-5 py-4">
                                    Satuan
                                </th>

                                <th class="px-5 py-4">
                                    Nilai Rujukan
                                </th>

                                <th class="px-5 py-4">
                                    Harga
                                </th>

                                <th class="px-5 py-4">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-center">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            <!-- LOADING -->

                            <tr v-if="loading">

                                <td
                                    colspan="8"
                                    class="px-5 py-12 text-center text-gray-500"
                                >

                                    <div
                                        class="flex flex-col items-center justify-center"
                                    >

                                        <div
                                            class="mb-3 h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-blue-600"
                                        ></div>

                                        <p>
                                            Memuat data pemeriksaan...
                                        </p>

                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr
                                v-else-if="filteredLabPemeriksaans.length === 0"
                            >

                                <td
                                    colspan="8"
                                    class="px-5 py-12 text-center"
                                >

                                    <div class="text-4xl">
                                        🧪
                                    </div>

                                    <p
                                        class="mt-3 font-medium text-gray-700"
                                    >
                                        Belum ada pemeriksaan
                                    </p>

                                    <p
                                        class="mt-1 text-sm text-gray-500"
                                    >
                                        Tambahkan master pemeriksaan
                                        laboratorium terlebih dahulu.
                                    </p>

                                </td>

                            </tr>


                            <!-- DATA -->

                            <tr
                                v-for="item in filteredLabPemeriksaans"
                                :key="item.id"
                                class="transition hover:bg-gray-50"
                            >

                                <!-- KODE -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 font-semibold text-gray-800"
                                >
                                    {{ item.kode }}
                                </td>


                                <!-- NAMA -->

                                <td class="px-5 py-4">

                                    <div
                                        class="font-medium text-gray-800"
                                    >
                                        {{ item.nama }}
                                    </div>

                                </td>


                                <!-- KATEGORI -->

                                <td class="px-5 py-4 text-gray-600">

                                    {{ item.kategori || '-' }}

                                </td>


                                <!-- SATUAN -->

                                <td class="px-5 py-4 text-gray-600">

                                    {{ item.satuan || '-' }}

                                </td>


                                <!-- NILAI RUJUKAN -->

                                <td
                                    class="max-w-xs px-5 py-4 text-gray-600"
                                >

                                    {{ item.nilai_rujukan || '-' }}

                                </td>


                                <!-- HARGA -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 font-medium text-gray-700"
                                >

                                    {{ formatRupiah(item.harga) }}

                                </td>


                                <!-- STATUS -->

                                <td class="px-5 py-4">

                                    <button
                                        @click="toggleStatus(item)"
                                        class="rounded-full px-3 py-1 text-xs font-semibold transition"
                                        :class="statusClass(item.is_active)"
                                    >

                                        {{
                                            item.is_active
                                                ? 'Aktif'
                                                : 'Nonaktif'
                                        }}

                                    </button>

                                </td>


                                <!-- AKSI -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-center"
                                >

                                    <button
                                        @click="openEditModal(item)"
                                        class="mr-2 rounded-lg bg-blue-100 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-200"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        @click="deleteLab(item)"
                                        class="rounded-lg bg-red-100 px-3 py-2 text-xs font-medium text-red-700 transition hover:bg-red-200"
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


        <!-- ========================================================= -->
        <!-- MODAL TAMBAH / EDIT -->
        <!-- ========================================================= -->

        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >

            <div
                class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-xl"
            >

                <!-- ================================================= -->
                <!-- MODAL HEADER -->
                <!-- ================================================= -->

                <div
                    class="flex items-center justify-between border-b px-6 py-5"
                >

                    <div>

                        <h2 class="text-xl font-bold text-gray-800">

                            {{
                                isEdit
                                    ? 'Edit Pemeriksaan Laboratorium'
                                    : 'Tambah Pemeriksaan Laboratorium'
                            }}

                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Lengkapi informasi pemeriksaan laboratorium.
                        </p>

                    </div>


                    <button
                        @click="closeModal"
                        class="text-2xl text-gray-400 transition hover:text-gray-700"
                    >
                        ×
                    </button>

                </div>


                <!-- ================================================= -->
                <!-- FORM -->
                <!-- ================================================= -->

                <form
                    @submit.prevent="submitForm"
                    class="space-y-5 p-6"
                >

                    <!-- KODE -->

                    <div>

                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Kode Pemeriksaan
                        </label>

                        <input
                            v-model="form.kode"
                            type="text"
                            required
                            placeholder="Contoh: LAB-001"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm uppercase outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />

                    </div>


                    <!-- NAMA -->

                    <div>

                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Nama Pemeriksaan
                        </label>

                        <input
                            v-model="form.nama"
                            type="text"
                            required
                            placeholder="Contoh: Hemoglobin"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />

                    </div>


                    <!-- KATEGORI + SATUAN -->

                    <div
                        class="grid grid-cols-1 gap-5 md:grid-cols-2"
                    >

                        <div>

                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Kategori
                            </label>

                            <input
                                v-model="form.kategori"
                                type="text"
                                placeholder="Contoh: Hematologi"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            />

                        </div>


                        <div>

                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Satuan
                            </label>

                            <input
                                v-model="form.satuan"
                                type="text"
                                placeholder="Contoh: g/dL"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            />

                        </div>

                    </div>


                    <!-- NILAI RUJUKAN -->

                    <div>

                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Nilai Rujukan
                        </label>

                        <input
                            v-model="form.nilai_rujukan"
                            type="text"
                            placeholder="Contoh: 13 - 17"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />

                        <p class="mt-1 text-xs text-gray-500">
                            Contoh: 13 - 17 g/dL, &lt; 100 mg/dL, Negatif.
                        </p>

                    </div>


                    <!-- HARGA -->

                    <div>

                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Harga
                        </label>

                        <div class="relative">

                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500"
                            >
                                Rp
                            </span>

                            <input
                                v-model.number="form.harga"
                                type="number"
                                min="0"
                                step="0.01"
                                required
                                class="w-full rounded-lg border border-gray-300 py-2.5 pl-11 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            />

                        </div>

                    </div>


                    <!-- STATUS -->

                    <div
                        class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 p-4"
                    >

                        <div>

                            <p class="text-sm font-medium text-gray-800">
                                Status Pemeriksaan
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Pemeriksaan nonaktif tidak akan tersedia
                                untuk permintaan laboratorium baru.
                            </p>

                        </div>


                        <label
                            class="relative inline-flex cursor-pointer items-center"
                        >

                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="peer sr-only"
                            />

                            <div
                                class="h-6 w-11 rounded-full bg-gray-300 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white"
                            ></div>

                        </label>

                    </div>


                    <!-- BUTTON -->

                    <div
                        class="flex justify-end gap-3 border-t pt-5"
                    >

                        <button
                            type="button"
                            @click="closeModal"
                            :disabled="submitting"
                            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:opacity-50"
                        >
                            Batal
                        </button>


                        <button
                            type="submit"
                            :disabled="submitting"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >

                            {{
                                submitting
                                    ? 'Menyimpan...'
                                    : isEdit
                                        ? 'Simpan Perubahan'
                                        : 'Simpan Pemeriksaan'
                            }}

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</template>
```

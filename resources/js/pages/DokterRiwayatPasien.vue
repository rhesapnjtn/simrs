<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const pemeriksaans = ref([])
const loading = ref(true)
const error = ref('')
const search = ref('')

/*
|--------------------------------------------------------------------------
| Ambil semua riwayat pasien dokter
|--------------------------------------------------------------------------
*/

const getRiwayatPasien = async () => {
    try {
        loading.value = true
        error.value = ''

        const response = await axios.get(
            '/api/dokter/pasien-riwayat'
        )

        pemeriksaans.value =
            response.data.pemeriksaans || []

    } catch (err) {
        console.error(err)

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil riwayat pasien.'
    } finally {
        loading.value = false
    }
}


/*
|--------------------------------------------------------------------------
| Buat daftar pasien unik
|--------------------------------------------------------------------------
|
| Satu pasien bisa memiliki beberapa pemeriksaan.
| Kita hanya tampilkan pasien satu kali.
|
*/

const pasienList = computed(() => {

    const map = new Map()

    pemeriksaans.value.forEach((pemeriksaan) => {

        const pasien =
            pemeriksaan.pendaftaran?.pasien

        const pendaftaran =
            pemeriksaan.pendaftaran

        if (!pasien) {
            return
        }

        if (!map.has(pasien.id)) {

            map.set(pasien.id, {
                id: pasien.id,
                no_rm: pasien.no_rm,
                nama: pasien.nama,
                nik: pasien.nik,
                jenis_kelamin: pasien.jenis_kelamin,
                no_telepon: pasien.no_telepon,

                poli:
                    pendaftaran?.poli?.nama_poli ||
                    pendaftaran?.poli?.nama ||
                    '-',

                kunjungan_terakhir:
                    pendaftaran?.tanggal_kunjungan ||
                    pemeriksaan.created_at ||
                    null,

                jumlah_kunjungan: 1
            })

        } else {

            const existing =
                map.get(pasien.id)

            existing.jumlah_kunjungan++

            /*
            |--------------------------------------------------------------
            | Ambil tanggal kunjungan terbaru
            |--------------------------------------------------------------
            */

            const tanggalBaru =
                pendaftaran?.tanggal_kunjungan ||
                pemeriksaan.created_at

            if (
                tanggalBaru &&
                (
                    !existing.kunjungan_terakhir ||
                    new Date(tanggalBaru) >
                    new Date(existing.kunjungan_terakhir)
                )
            ) {
                existing.kunjungan_terakhir =
                    tanggalBaru
            }
        }
    })

    return Array.from(map.values())
})


/*
|--------------------------------------------------------------------------
| Search pasien
|--------------------------------------------------------------------------
*/

const filteredPasien = computed(() => {

    const keyword =
        search.value
            .toLowerCase()
            .trim()

    if (!keyword) {
        return pasienList.value
    }

    return pasienList.value.filter((pasien) => {

        return (
            pasien.nama
                ?.toLowerCase()
                .includes(keyword)

            ||

            pasien.no_rm
                ?.toLowerCase()
                .includes(keyword)

            ||

            pasien.nik
                ?.toLowerCase()
                .includes(keyword)
        )
    })
})


/*
|--------------------------------------------------------------------------
| Lihat seluruh riwayat pasien
|--------------------------------------------------------------------------
*/

const lihatRiwayat = (pasienId) => {

    router.push(
        `/pasien/${pasienId}/riwayat-pemeriksaan`
    )
}


/*
|--------------------------------------------------------------------------
| Format tanggal
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
            month: 'long',
            year: 'numeric'
        }
    )
}


/*
|--------------------------------------------------------------------------
| Jenis kelamin
|--------------------------------------------------------------------------
*/

const jenisKelamin = (jenis) => {

    if (jenis === 'L') {
        return 'Laki-laki'
    }

    if (jenis === 'P') {
        return 'Perempuan'
    }

    return '-'
}


/*
|--------------------------------------------------------------------------
| Statistik
|--------------------------------------------------------------------------
*/

const totalPasien = computed(() => {
    return pasienList.value.length
})

const totalPemeriksaan = computed(() => {
    return pemeriksaans.value.length
})


/*
|--------------------------------------------------------------------------
| Load
|--------------------------------------------------------------------------
*/

onMounted(() => {
    getRiwayatPasien()
})
</script>


<template>

    <div class="min-h-screen bg-slate-100">

        <!-- Navbar -->
        <header
            class="bg-white border-b border-slate-200"
        >

            <div
                class="h-16 px-6 flex items-center
                       justify-between"
            >

                <!-- Logo -->
                <div class="flex items-center gap-3">

                    <div
                        class="w-9 h-9 rounded-lg
                               bg-blue-600
                               flex items-center
                               justify-center
                               text-white font-bold"
                    >
                        S
                    </div>

                    <div>

                        <h1
                            class="font-bold
                                   text-slate-800"
                        >
                            SIMRS
                        </h1>

                        <p
                            class="text-xs
                                   text-slate-500"
                        >
                            Hospital Management System
                        </p>

                    </div>

                </div>


                <!-- Back -->
                <button
                    @click="router.push('/dashboard')"
                    class="text-sm
                           text-slate-600
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
                class="mb-6"
            >

                <h2
                    class="text-2xl
                           font-bold
                           text-slate-800"
                >
                    Riwayat Pasien
                </h2>

                <p
                    class="mt-1
                           text-slate-500"
                >
                    Daftar seluruh pasien yang pernah
                    diperiksa oleh Anda.
                </p>

            </div>


            <!-- Statistik -->
            <div
                class="grid
                       grid-cols-1
                       md:grid-cols-2
                       gap-4
                       mb-6"
            >

                <!-- Total Pasien -->
                <div
                    class="bg-white
                           rounded-2xl
                           border
                           border-slate-200
                           p-5"
                >

                    <div
                        class="flex
                               items-center
                               justify-between"
                    >

                        <div>

                            <p
                                class="text-sm
                                       text-slate-500"
                            >
                                Total Pasien
                            </p>

                            <p
                                class="mt-1
                                       text-3xl
                                       font-bold
                                       text-slate-800"
                            >
                                {{ totalPasien }}
                            </p>

                        </div>

                        <div
                            class="w-12 h-12
                                   rounded-xl
                                   bg-blue-100
                                   flex items-center
                                   justify-center
                                   text-xl"
                        >
                            👥
                        </div>

                    </div>

                </div>


                <!-- Total Pemeriksaan -->
                <div
                    class="bg-white
                           rounded-2xl
                           border
                           border-slate-200
                           p-5"
                >

                    <div
                        class="flex
                               items-center
                               justify-between"
                    >

                        <div>

                            <p
                                class="text-sm
                                       text-slate-500"
                            >
                                Total Pemeriksaan
                            </p>

                            <p
                                class="mt-1
                                       text-3xl
                                       font-bold
                                       text-slate-800"
                            >
                                {{ totalPemeriksaan }}
                            </p>

                        </div>

                        <div
                            class="w-12 h-12
                                   rounded-xl
                                   bg-green-100
                                   flex items-center
                                   justify-center
                                   text-xl"
                        >
                            🩺
                        </div>

                    </div>

                </div>

            </div>


            <!-- Error -->
            <div
                v-if="error"
                class="mb-5
                       rounded-xl
                       border
                       border-red-200
                       bg-red-50
                       p-4
                       text-red-700"
            >
                {{ error }}
            </div>


            <!-- Search -->
            <div
                class="mb-5
                       bg-white
                       rounded-2xl
                       border
                       border-slate-200
                       p-4"
            >

                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama pasien, No. RM, atau NIK..."
                    class="w-full
                           rounded-xl
                           border
                           border-slate-300
                           px-4
                           py-3
                           focus:border-blue-500
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-100"
                >

            </div>


            <!-- Loading -->
            <div
                v-if="loading"
                class="rounded-2xl
                       border
                       border-slate-200
                       bg-white
                       p-10
                       text-center"
            >

                <p class="text-slate-500">
                    Memuat data riwayat pasien...
                </p>

            </div>


            <!-- Table -->
            <div
                v-else
                class="overflow-hidden
                       rounded-2xl
                       border
                       border-slate-200
                       bg-white"
            >

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead
                            class="bg-slate-50"
                        >

                            <tr>

                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    #
                                </th>

                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    No. RM
                                </th>

                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    Pasien
                                </th>

                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    Jenis Kelamin
                                </th>

                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    Poli
                                </th>

                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           font-semibold
                                           text-slate-500"
                                >
                                    Kunjungan Terakhir
                                </th>

                                <th
                                    class="px-6
                                           py-4
                                           text-center
                                           font-semibold
                                           text-slate-500"
                                >
                                    Kunjungan
                                </th>

                                <th
                                    class="px-6
                                           py-4
                                           text-right
                                           font-semibold
                                           text-slate-500"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <!-- Empty -->
                            <tr
                                v-if="
                                    filteredPasien.length === 0
                                "
                            >

                                <td
                                    colspan="8"
                                    class="px-6
                                           py-12
                                           text-center
                                           text-slate-500"
                                >

                                    <div
                                        class="text-4xl
                                               mb-3"
                                    >
                                        📋
                                    </div>

                                    <p
                                        class="font-medium
                                               text-slate-700"
                                    >
                                        Belum ada pasien
                                    </p>

                                    <p
                                        class="mt-1
                                               text-sm"
                                    >
                                        Tidak ditemukan pasien
                                        yang pernah diperiksa.
                                    </p>

                                </td>

                            </tr>


                            <!-- Data -->
                            <tr
                                v-for="(
                                    pasien,
                                    index
                                ) in filteredPasien"
                                :key="pasien.id"
                                class="border-t
                                       border-slate-100
                                       hover:bg-slate-50"
                            >

                                <td
                                    class="px-6 py-4"
                                >
                                    {{ index + 1 }}
                                </td>


                                <td
                                    class="px-6 py-4"
                                >

                                    <span
                                        class="font-semibold
                                               text-blue-600"
                                    >
                                        {{ pasien.no_rm }}
                                    </span>

                                </td>


                                <td
                                    class="px-6 py-4"
                                >

                                    <p
                                        class="font-semibold
                                               text-slate-800"
                                    >
                                        {{ pasien.nama }}
                                    </p>

                                    <p
                                        v-if="pasien.nik"
                                        class="mt-1
                                               text-xs
                                               text-slate-400"
                                    >
                                        NIK:
                                        {{ pasien.nik }}
                                    </p>

                                </td>


                                <td
                                    class="px-6 py-4
                                           text-slate-600"
                                >
                                    {{
                                        jenisKelamin(
                                            pasien.jenis_kelamin
                                        )
                                    }}
                                </td>


                                <td
                                    class="px-6 py-4
                                           text-slate-600"
                                >
                                    {{ pasien.poli }}
                                </td>


                                <td
                                    class="px-6 py-4
                                           text-slate-600"
                                >
                                    {{
                                        formatTanggal(
                                            pasien.kunjungan_terakhir
                                        )
                                    }}
                                </td>


                                <td
                                    class="px-6 py-4
                                           text-center"
                                >

                                    <span
                                        class="inline-flex
                                               min-w-8
                                               justify-center
                                               rounded-full
                                               bg-blue-100
                                               px-3
                                               py-1
                                               text-xs
                                               font-semibold
                                               text-blue-700"
                                    >
                                        {{
                                            pasien.jumlah_kunjungan
                                        }}
                                    </span>

                                </td>


                                <td
                                    class="px-6 py-4
                                           text-right"
                                >

                                    <button
                                        @click="
                                            lihatRiwayat(
                                                pasien.id
                                            )
                                        "
                                        class="rounded-lg
                                               bg-green-50
                                               px-4
                                               py-2
                                               font-medium
                                               text-green-600
                                               hover:bg-green-100"
                                    >
                                        Lihat Riwayat
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </main>

    </div>

</template>
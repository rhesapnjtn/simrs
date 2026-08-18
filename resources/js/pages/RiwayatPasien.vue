<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const pasien = ref(null);
const pemeriksaans = ref([]);

const loading = ref(true);
const error = ref('');


// =====================================================
// AMBIL RIWAYAT PASIEN
// =====================================================

const getRiwayat = async () => {
    try {
        loading.value = true;
        error.value = '';

        const response = await axios.get(
            `/api/pasien/${route.params.id}/riwayat-pemeriksaan`
        );

        pemeriksaans.value =
            response.data.pemeriksaans || [];

        // Ambil identitas pasien dari pemeriksaan pertama
        if (pemeriksaans.value.length > 0) {
            pasien.value =
                pemeriksaans.value[0]
                    ?.pendaftaran
                    ?.pasien;
        }

    } catch (err) {
        console.error(
            'Gagal mengambil riwayat pasien:',
            err
        );

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil riwayat pasien.';

    } finally {
        loading.value = false;
    }
};


// =====================================================
// KEMBALI
// =====================================================

const kembali = () => {
    router.push('/pasiens');
};


// =====================================================
// FORMAT TANGGAL
// =====================================================

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-';

    return new Date(tanggal).toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }
    );
};


// =====================================================
// INIT
// =====================================================

onMounted(() => {
    getRiwayat();
});
</script>


<template>

    <div class="min-h-screen bg-slate-100">

        <!-- ================================================= -->
        <!-- NAVBAR -->
        <!-- ================================================= -->

        <header
            class="bg-white
                   border-b border-slate-200"
        >

            <div
                class="h-16 px-6
                       flex items-center
                       justify-between"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="w-9 h-9
                               rounded-lg
                               bg-blue-600
                               flex items-center
                               justify-center
                               text-white
                               font-bold"
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


                <button
                    @click="kembali"
                    class="text-sm
                           text-slate-600
                           hover:text-blue-600"
                >
                    ← Master Pasien
                </button>

            </div>

        </header>


        <!-- ================================================= -->
        <!-- CONTENT -->
        <!-- ================================================= -->

        <main class="p-6">

            <div class="max-w-5xl mx-auto">


                <!-- ================================================= -->
                <!-- HEADER -->
                <!-- ================================================= -->

                <div class="mb-6">

                    <h2
                        class="text-2xl
                               font-bold
                               text-slate-800"
                    >
                        Riwayat Pasien
                    </h2>

                    <p
                        class="text-slate-500
                               mt-1"
                    >
                        Riwayat pemeriksaan medis pasien.
                    </p>

                </div>


                <!-- ================================================= -->
                <!-- LOADING -->
                <!-- ================================================= -->

                <div
                    v-if="loading"
                    class="bg-white
                           rounded-2xl
                           border border-slate-200
                           p-10
                           text-center"
                >

                    <p class="text-slate-500">
                        Memuat riwayat pasien...
                    </p>

                </div>


                <!-- ================================================= -->
                <!-- ERROR -->
                <!-- ================================================= -->

                <div
                    v-else-if="error"
                    class="bg-red-50
                           border border-red-200
                           text-red-700
                           rounded-xl
                           p-5"
                >

                    {{ error }}

                </div>


                <!-- ================================================= -->
                <!-- DATA -->
                <!-- ================================================= -->

                <div
                    v-else
                    class="space-y-6"
                >


                    <!-- ================================================= -->
                    <!-- IDENTITAS PASIEN -->
                    <!-- ================================================= -->

                    <div
                        class="bg-white
                               rounded-2xl
                               border border-slate-200
                               p-6"
                    >

                        <div
                            class="flex flex-col
                                   md:flex-row
                                   md:items-center
                                   md:justify-between
                                   gap-5"
                        >

                            <div>

                                <p
                                    class="text-xs
                                           uppercase
                                           tracking-wide
                                           text-slate-400
                                           mb-1"
                                >
                                    Pasien
                                </p>

                                <h3
                                    class="text-xl
                                           font-bold
                                           text-slate-800"
                                >
                                    {{
                                        pasien?.nama ||
                                        pemeriksaans[0]
                                            ?.pendaftaran
                                            ?.pasien
                                            ?.nama ||
                                        'Pasien'
                                    }}
                                </h3>

                                <p
                                    class="text-sm
                                           text-slate-500
                                           mt-1"
                                >
                                    RM:
                                    {{
                                        pasien?.no_rm ||
                                        pemeriksaans[0]
                                            ?.pendaftaran
                                            ?.pasien
                                            ?.no_rm ||
                                        '-'
                                    }}
                                </p>

                            </div>


                            <div
                                class="text-left
                                       md:text-right"
                            >

                                <p
                                    class="text-xs
                                           text-slate-400
                                           mb-1"
                                >
                                    Total Pemeriksaan
                                </p>

                                <p
                                    class="text-2xl
                                           font-bold
                                           text-blue-600"
                                >
                                    {{ pemeriksaans.length }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- BELUM ADA RIWAYAT -->
                    <!-- ================================================= -->

                    <div
                        v-if="pemeriksaans.length === 0"
                        class="bg-white
                               rounded-2xl
                               border border-slate-200
                               p-10
                               text-center"
                    >

                        <p
                            class="text-slate-500"
                        >
                            Belum ada riwayat pemeriksaan
                            untuk pasien ini.
                        </p>

                    </div>


                    <!-- ================================================= -->
                    <!-- RIWAYAT -->
                    <!-- ================================================= -->

                    <div
                        v-for="pemeriksaan in pemeriksaans"
                        :key="pemeriksaan.id"
                        class="bg-white
                               rounded-2xl
                               border border-slate-200
                               overflow-hidden"
                    >


                        <!-- HEADER RIWAYAT -->

                        <div
                            class="px-6 py-5
                                   border-b border-slate-200
                                   bg-slate-50"
                        >

                            <div
                                class="flex flex-col
                                       md:flex-row
                                       md:items-center
                                       md:justify-between
                                       gap-3"
                            >

                                <div>

                                    <p
                                        class="text-sm
                                               font-semibold
                                               text-slate-800"
                                    >
                                        {{
                                            formatTanggal(
                                                pemeriksaan
                                                    .pendaftaran
                                                    ?.tanggal_kunjungan
                                            )
                                        }}
                                    </p>

                                    <p
                                        class="text-sm
                                               text-slate-500
                                               mt-1"
                                    >
                                        {{
                                            pemeriksaan
                                                .pendaftaran
                                                ?.poli
                                                ?.nama ||
                                            '-'
                                        }}

                                        ·

                                        {{
                                            pemeriksaan
                                                .pendaftaran
                                                ?.dokter
                                                ?.nama ||
                                            '-'
                                        }}
                                    </p>

                                </div>


                                <span
                                    class="px-3 py-1
                                           rounded-full
                                           bg-green-100
                                           text-green-700
                                           text-xs
                                           font-medium
                                           w-fit"
                                >
                                    DIPERIKSA
                                </span>

                            </div>

                        </div>


                        <!-- BODY -->

                        <div class="p-6 space-y-6">


                            <!-- ================================================= -->
                            <!-- KELUHAN -->
                            <!-- ================================================= -->

                            <div>

                                <h4
                                    class="text-sm
                                           font-semibold
                                           text-slate-800
                                           mb-2"
                                >
                                    Keluhan
                                </h4>

                                <p
                                    class="text-sm
                                           text-slate-600"
                                >
                                    {{
                                        pemeriksaan
                                            .pendaftaran
                                            ?.keluhan ||
                                        '-'
                                    }}
                                </p>

                            </div>


                            <!-- ================================================= -->
                            <!-- TANDA VITAL -->
                            <!-- ================================================= -->

                            <div>

                                <h4
                                    class="text-sm
                                           font-semibold
                                           text-slate-800
                                           mb-3"
                                >
                                    Tanda Vital
                                </h4>


                                <div
                                    class="grid
                                           grid-cols-2
                                           md:grid-cols-3
                                           lg:grid-cols-6
                                           gap-3"
                                >


                                    <div
                                        class="p-3
                                               rounded-xl
                                               bg-slate-50
                                               border border-slate-200"
                                    >

                                        <p
                                            class="text-xs
                                                   text-slate-400"
                                        >
                                            Tekanan Darah
                                        </p>

                                        <p
                                            class="font-semibold
                                                   text-slate-800
                                                   mt-1"
                                        >
                                            {{
                                                pemeriksaan
                                                    .tekanan_darah ||
                                                '-'
                                            }}
                                        </p>

                                    </div>


                                    <div
                                        class="p-3
                                               rounded-xl
                                               bg-slate-50
                                               border border-slate-200"
                                    >

                                        <p
                                            class="text-xs
                                                   text-slate-400"
                                        >
                                            Suhu
                                        </p>

                                        <p
                                            class="font-semibold
                                                   text-slate-800
                                                   mt-1"
                                        >
                                            {{
                                                pemeriksaan.suhu ||
                                                '-'
                                            }}
                                            °C
                                        </p>

                                    </div>


                                    <div
                                        class="p-3
                                               rounded-xl
                                               bg-slate-50
                                               border border-slate-200"
                                    >

                                        <p
                                            class="text-xs
                                                   text-slate-400"
                                        >
                                            Berat Badan
                                        </p>

                                        <p
                                            class="font-semibold
                                                   text-slate-800
                                                   mt-1"
                                        >
                                            {{
                                                pemeriksaan
                                                    .berat_badan ||
                                                '-'
                                            }}
                                            kg
                                        </p>

                                    </div>


                                    <div
                                        class="p-3
                                               rounded-xl
                                               bg-slate-50
                                               border border-slate-200"
                                    >

                                        <p
                                            class="text-xs
                                                   text-slate-400"
                                        >
                                            Tinggi Badan
                                        </p>

                                        <p
                                            class="font-semibold
                                                   text-slate-800
                                                   mt-1"
                                        >
                                            {{
                                                pemeriksaan
                                                    .tinggi_badan ||
                                                '-'
                                            }}
                                            cm
                                        </p>

                                    </div>


                                    <div
                                        class="p-3
                                               rounded-xl
                                               bg-slate-50
                                               border border-slate-200"
                                    >

                                        <p
                                            class="text-xs
                                                   text-slate-400"
                                        >
                                            Nadi
                                        </p>

                                        <p
                                            class="font-semibold
                                                   text-slate-800
                                                   mt-1"
                                        >
                                            {{
                                                pemeriksaan.nadi ||
                                                '-'
                                            }}
                                            x/menit
                                        </p>

                                    </div>


                                    <div
                                        class="p-3
                                               rounded-xl
                                               bg-slate-50
                                               border border-slate-200"
                                    >

                                        <p
                                            class="text-xs
                                                   text-slate-400"
                                        >
                                            Respirasi
                                        </p>

                                        <p
                                            class="font-semibold
                                                   text-slate-800
                                                   mt-1"
                                        >
                                            {{
                                                pemeriksaan
                                                    .respirasi ||
                                                '-'
                                            }}
                                            x/menit
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- ================================================= -->
                            <!-- DIAGNOSIS -->
                            <!-- ================================================= -->

                            <div>

                                <h4
                                    class="text-sm
                                           font-semibold
                                           text-slate-800
                                           mb-2"
                                >
                                    Diagnosis
                                </h4>

                                <div
                                    class="p-4
                                           rounded-xl
                                           bg-slate-50
                                           border border-slate-200
                                           text-sm
                                           text-slate-700"
                                >
                                    {{
                                        pemeriksaan.diagnosis ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <!-- ================================================= -->
                            <!-- TINDAKAN -->
                            <!-- ================================================= -->

                            <div>

                                <h4
                                    class="text-sm
                                           font-semibold
                                           text-slate-800
                                           mb-2"
                                >
                                    Tindakan
                                </h4>

                                <div
                                    class="p-4
                                           rounded-xl
                                           bg-slate-50
                                           border border-slate-200
                                           text-sm
                                           text-slate-700"
                                >
                                    {{
                                        pemeriksaan.tindakan ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <!-- ================================================= -->
                            <!-- CATATAN -->
                            <!-- ================================================= -->

                            <div>

                                <h4
                                    class="text-sm
                                           font-semibold
                                           text-slate-800
                                           mb-2"
                                >
                                    Catatan
                                </h4>

                                <div
                                    class="p-4
                                           rounded-xl
                                           bg-slate-50
                                           border border-slate-200
                                           text-sm
                                           text-slate-700"
                                >
                                    {{
                                        pemeriksaan.catatan ||
                                        '-'
                                    }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </main>

    </div>

</template>
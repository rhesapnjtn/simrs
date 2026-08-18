```vue
<script setup>

import {
    ref,
    computed,
    onMounted
} from 'vue';

import axios from 'axios';

import {
    useRouter
} from 'vue-router';


// =====================================================
// ROUTER
// =====================================================

const router = useRouter();


// =====================================================
// USER
// =====================================================

const user = ref(null);

const loadingUser = ref(true);


// =====================================================
// DASHBOARD
// =====================================================

const loadingDashboard = ref(false);

const dashboardError = ref('');


// =====================================================
// STATISTICS
// =====================================================

const statistics = ref({

    pasien_hari_ini: 0,

    menunggu: 0,

    sedang_diperiksa: 0,

    selesai: 0

});


// =====================================================
// DATA
// =====================================================

const antrian = ref([]);

const poli = ref([]);

const aktivitas = ref([]);


// =====================================================
// COMPUTED USER
// =====================================================

const userName = computed(() => {

    return user.value?.name || 'User';

});


const userRoles = computed(() => {

    if (!user.value) {
        return [];
    }


    if (Array.isArray(user.value.roles)) {

        return user.value.roles
            .map(role => {

                if (typeof role === 'string') {

                    return role.toUpperCase();

                }

                return role?.name?.toUpperCase();

            })
            .filter(Boolean);

    }


    return [];

});


const userRole = computed(() => {

    return userRoles.value[0] || 'USER';

});


const userInitial = computed(() => {

    return userName.value
        .substring(0, 2)
        .toUpperCase();

});


// =====================================================
// ROLE
// =====================================================

const isSuperAdmin = computed(() => {

    return userRoles.value.includes(
        'SUPER_ADMIN'
    );

});


const isDokter = computed(() => {

    return userRoles.value.includes(
        'DOKTER'
    );

});


const isPerawat = computed(() => {

    return userRoles.value.includes(
        'PERAWAT'
    );

});


const isFarmasi = computed(() => {

    return userRoles.value.includes(
        'FARMASI'
    );

});


// =====================================================
// GET USER
// =====================================================

const getUser = async () => {

    try {

        loadingUser.value = true;


        const response = await axios.get(
            '/api/user'
        );


        user.value =
            response.data.user ||
            response.data;


    } catch (error) {

        console.error(
            'Gagal mengambil user:',
            error
        );


        router.push('/login');

    } finally {

        loadingUser.value = false;

    }

};


// =====================================================
// GET DASHBOARD
// =====================================================

const getDashboard = async () => {

    try {

        loadingDashboard.value = true;

        dashboardError.value = '';


        /*
        |--------------------------------------------------------------------------
        | DATA PENDAFTARAN
        |--------------------------------------------------------------------------
        */

        const pendaftaranResponse =
            await axios.get(
                '/api/pendaftarans'
            );


        const pendaftarans =
            pendaftaranResponse.data.pendaftarans ||
            pendaftaranResponse.data.data ||
            pendaftaranResponse.data ||
            [];


        /*
        |--------------------------------------------------------------------------
        | TANGGAL HARI INI
        |--------------------------------------------------------------------------
        */

        const today =
            new Date()
                .toISOString()
                .split('T')[0];


        /*
        |--------------------------------------------------------------------------
        | FILTER PENDAFTARAN HARI INI
        |--------------------------------------------------------------------------
        */

        const hariIni =
            pendaftarans.filter(item => {

                const tanggal =
                    item.tanggal_kunjungan ||
                    item.tanggal ||
                    item.created_at;


                if (!tanggal) {
                    return false;
                }


                return String(tanggal)
                    .substring(0, 10) === today;

            });


        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        const menunggu =
            hariIni.filter(item => {

                const status =
                    String(
                        item.status || ''
                    ).toUpperCase();


                return [
                    'MENUNGGU',
                    'WAITING',
                    'TERDAFTAR'
                ].includes(status);

            }).length;


        const sedangDiperiksa =
            hariIni.filter(item => {

                const status =
                    String(
                        item.status || ''
                    ).toUpperCase();


                return [
                    'DIPERIKSA',
                    'SEDANG_DIPERIKSA',
                    'IN_PROGRESS'
                ].includes(status);

            }).length;


        const selesai =
            hariIni.filter(item => {

                const status =
                    String(
                        item.status || ''
                    ).toUpperCase();


                return [
                    'SELESAI',
                    'COMPLETED'
                ].includes(status);

            }).length;


        statistics.value = {

            pasien_hari_ini:
                hariIni.length,

            menunggu,

            sedang_diperiksa:
                sedangDiperiksa,

            selesai

        };


        /*
        |--------------------------------------------------------------------------
        | ANTRIAN
        |--------------------------------------------------------------------------
        */

        antrian.value =
            hariIni
                .sort((a, b) => {

                    return (
                        Number(
                            a.no_antrian
                                ?.replace(/\D/g, '')
                        ) || 0
                    ) -
                    (
                        Number(
                            b.no_antrian
                                ?.replace(/\D/g, '')
                        ) || 0
                    );

                })
                .slice(0, 10);


        /*
        |--------------------------------------------------------------------------
        | POLI
        |--------------------------------------------------------------------------
        */

        const poliMap = {};


        hariIni.forEach(item => {

            const namaPoli =
                item.poli?.nama ||
                item.poli?.nama_poli ||
                'Tidak diketahui';


            if (!poliMap[namaPoli]) {

                poliMap[namaPoli] = 0;

            }


            poliMap[namaPoli]++;

        });


        poli.value =
            Object.entries(poliMap)
                .map(([nama, jumlah]) => ({
                    nama,
                    jumlah
                }))
                .sort(
                    (a, b) =>
                        b.jumlah - a.jumlah
                );


        /*
        |--------------------------------------------------------------------------
        | AKTIVITAS TERBARU
        |--------------------------------------------------------------------------
        */

        aktivitas.value =
            hariIni
                .slice()
                .reverse()
                .slice(0, 5)
                .map(item => ({

                    id: item.id,

                    pasien:
                        item.pasien?.nama ||
                        'Pasien',

                    poli:
                        item.poli?.nama ||
                        '-',

                    dokter:
                        item.dokter?.nama ||
                        '-',

                    status:
                        item.status ||
                        'MENUNGGU'

                }));


    } catch (error) {

        console.error(
            'Gagal mengambil dashboard:',
            error
        );


        dashboardError.value =
            error.response?.data?.message ||
            'Gagal mengambil data dashboard.';

    } finally {

        loadingDashboard.value = false;

    }

};


// =====================================================
// REFRESH
// =====================================================

const refreshDashboard = async () => {

    await getDashboard();

};


// =====================================================
// LOGOUT
// =====================================================

const logout = async () => {

    try {

        await axios.post(
            '/api/logout'
        );

    } catch (error) {

        console.error(
            'Logout error:',
            error
        );

    } finally {

        user.value = null;

        router.push('/login');

    }

};


// =====================================================
// STATUS LABEL
// =====================================================

const statusLabel = (status) => {

    const value =
        String(status || '')
            .toUpperCase();


    switch (value) {

        case 'MENUNGGU':
        case 'WAITING':
        case 'TERDAFTAR':

            return 'Menunggu';


        case 'DIPERIKSA':
        case 'SEDANG_DIPERIKSA':
        case 'IN_PROGRESS':

            return 'Sedang Diperiksa';


        case 'SELESAI':
        case 'COMPLETED':

            return 'Selesai';


        case 'DIBATALKAN':
        case 'CANCELLED':

            return 'Dibatalkan';


        default:

            return status || '-';

    }

};


// =====================================================
// STATUS CLASS
// =====================================================

const statusClass = (status) => {

    const value =
        String(status || '')
            .toUpperCase();


    switch (value) {

        case 'MENUNGGU':
        case 'WAITING':
        case 'TERDAFTAR':

            return 'bg-yellow-100 text-yellow-700';


        case 'DIPERIKSA':
        case 'SEDANG_DIPERIKSA':
        case 'IN_PROGRESS':

            return 'bg-blue-100 text-blue-700';


        case 'SELESAI':
        case 'COMPLETED':

            return 'bg-green-100 text-green-700';


        case 'DIBATALKAN':
        case 'CANCELLED':

            return 'bg-red-100 text-red-700';


        default:

            return 'bg-slate-100 text-slate-600';

    }

};


// =====================================================
// FORMAT DATE
// =====================================================

const formatDate = () => {

    return new Date().toLocaleDateString(
        'id-ID',
        {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }
    );

};


// =====================================================
// MOUNTED
// =====================================================

onMounted(async () => {

    await getUser();

    await getDashboard();

});

</script>


<template>

<div class="min-h-screen bg-slate-100">


    <!-- ================================================= -->
    <!-- NAVBAR -->
    <!-- ================================================= -->

    <header
        class="
            bg-white
            border-b
            border-slate-200
        "
    >

        <div
            class="
                h-16
                px-6
                flex
                items-center
                justify-between
            "
        >

            <!-- LOGO -->

            <div
                class="
                    flex
                    items-center
                    gap-3
                "
            >

                <div
                    class="
                        w-9
                        h-9
                        rounded-lg
                        bg-blue-600
                        flex
                        items-center
                        justify-center
                        text-white
                        font-bold
                    "
                >

                    S

                </div>


                <div>

                    <h1
                        class="
                            font-bold
                            text-slate-800
                        "
                    >

                        SIMRS

                    </h1>


                    <p
                        class="
                            text-xs
                            text-slate-500
                        "
                    >

                        Hospital Management System

                    </p>

                </div>

            </div>


            <!-- USER -->

            <div
                class="
                    flex
                    items-center
                    gap-4
                "
            >

                <div
                    class="
                        hidden
                        sm:block
                        text-right
                    "
                >

                    <p
                        class="
                            text-sm
                            font-semibold
                            text-slate-800
                        "
                    >

                        {{ userName }}

                    </p>


                    <p
                        class="
                            text-xs
                            text-slate-500
                        "
                    >

                        {{ userRole }}

                    </p>

                </div>


                <!-- AVATAR -->

                <div
                    class="
                        w-10
                        h-10
                        rounded-full
                        bg-blue-100
                        flex
                        items-center
                        justify-center
                        text-blue-600
                        font-bold
                    "
                >

                    {{ userInitial }}

                </div>


                <!-- LOGOUT -->

                <button
                    @click="logout"
                    class="
                        px-4
                        py-2
                        rounded-lg
                        bg-red-600
                        text-white
                        text-sm
                        font-medium
                        hover:bg-red-700
                        transition
                    "
                >

                    Logout

                </button>

            </div>

        </div>

    </header>


    <!-- ================================================= -->
    <!-- CONTENT -->
    <!-- ================================================= -->

    <main class="p-6">


        <!-- ================================================= -->
        <!-- HEADER -->
        <!-- ================================================= -->

        <div
            class="
                mb-6
                flex
                flex-col
                md:flex-row
                md:items-center
                md:justify-between
                gap-4
            "
        >

            <div>

                <h2
                    class="
                        text-2xl
                        font-bold
                        text-slate-800
                    "
                >

                    Dashboard

                </h2>


                <p
                    class="
                        text-slate-500
                        mt-1
                    "
                >

                    Ringkasan aktivitas rumah sakit hari ini.

                </p>


                <p
                    class="
                        text-xs
                        text-slate-400
                        mt-1
                    "
                >

                    {{ formatDate() }}

                </p>

            </div>


            <!-- REFRESH -->

            <button
                @click="refreshDashboard"
                :disabled="loadingDashboard"
                class="
                    inline-flex
                    items-center
                    justify-center
                    gap-2
                    px-4
                    py-2
                    rounded-lg
                    bg-white
                    border
                    border-slate-200
                    text-sm
                    font-medium
                    text-slate-700
                    hover:bg-slate-50
                    disabled:opacity-50
                    transition
                "
            >

                <span
                    :class="{
                        'animate-spin':
                            loadingDashboard
                    }"
                >
                    ↻
                </span>

                Refresh

            </button>

        </div>


        <!-- ================================================= -->
        <!-- ERROR -->
        <!-- ================================================= -->

        <div
            v-if="dashboardError"
            class="
                mb-5
                p-4
                rounded-xl
                bg-red-50
                border
                border-red-200
                text-red-700
                text-sm
            "
        >

            {{ dashboardError }}

        </div>


        <!-- ================================================= -->
        <!-- STATISTICS -->
        <!-- ================================================= -->

        <div
            class="
                grid
                grid-cols-1
                sm:grid-cols-2
                xl:grid-cols-4
                gap-5
            "
        >


            <!-- PASIEN -->

            <div
                class="
                    bg-white
                    rounded-2xl
                    p-5
                    border
                    border-slate-200
                "
            >

                <div
                    class="
                        flex
                        items-start
                        justify-between
                    "
                >

                    <div>

                        <p
                            class="
                                text-sm
                                text-slate-500
                            "
                        >

                            Pasien Hari Ini

                        </p>


                        <h3
                            class="
                                text-3xl
                                font-bold
                                text-slate-800
                                mt-2
                            "
                        >

                            {{ statistics.pasien_hari_ini }}

                        </h3>

                    </div>


                    <div
                        class="
                            w-11
                            h-11
                            rounded-xl
                            bg-blue-50
                            flex
                            items-center
                            justify-center
                            text-xl
                        "
                    >

                        🧑

                    </div>

                </div>


                <p
                    class="
                        text-xs
                        text-slate-400
                        mt-4
                    "
                >

                    Total pasien yang terdaftar hari ini.

                </p>

            </div>


            <!-- MENUNGGU -->

            <div
                class="
                    bg-white
                    rounded-2xl
                    p-5
                    border
                    border-slate-200
                "
            >

                <div
                    class="
                        flex
                        items-start
                        justify-between
                    "
                >

                    <div>

                        <p
                            class="
                                text-sm
                                text-slate-500
                            "
                        >

                            Menunggu

                        </p>


                        <h3
                            class="
                                text-3xl
                                font-bold
                                text-slate-800
                                mt-2
                            "
                        >

                            {{ statistics.menunggu }}

                        </h3>

                    </div>


                    <div
                        class="
                            w-11
                            h-11
                            rounded-xl
                            bg-yellow-50
                            flex
                            items-center
                            justify-center
                            text-xl
                        "
                    >

                        🎫

                    </div>

                </div>


                <p
                    class="
                        text-xs
                        text-yellow-600
                        mt-4
                    "
                >

                    Pasien dalam antrean.

                </p>

            </div>


            <!-- DIPERIKSA -->

            <div
                class="
                    bg-white
                    rounded-2xl
                    p-5
                    border
                    border-slate-200
                "
            >

                <div
                    class="
                        flex
                        items-start
                        justify-between
                    "
                >

                    <div>

                        <p
                            class="
                                text-sm
                                text-slate-500
                            "
                        >

                            Sedang Diperiksa

                        </p>


                        <h3
                            class="
                                text-3xl
                                font-bold
                                text-slate-800
                                mt-2
                            "
                        >

                            {{ statistics.sedang_diperiksa }}

                        </h3>

                    </div>


                    <div
                        class="
                            w-11
                            h-11
                            rounded-xl
                            bg-blue-50
                            flex
                            items-center
                            justify-center
                            text-xl
                        "
                    >

                        🩺

                    </div>

                </div>


                <p
                    class="
                        text-xs
                        text-blue-600
                        mt-4
                    "
                >

                    Sedang mendapatkan pelayanan.

                </p>

            </div>


            <!-- SELESAI -->

            <div
                class="
                    bg-white
                    rounded-2xl
                    p-5
                    border
                    border-slate-200
                "
            >

                <div
                    class="
                        flex
                        items-start
                        justify-between
                    "
                >

                    <div>

                        <p
                            class="
                                text-sm
                                text-slate-500
                            "
                        >

                            Selesai

                        </p>


                        <h3
                            class="
                                text-3xl
                                font-bold
                                text-slate-800
                                mt-2
                            "
                        >

                            {{ statistics.selesai }}

                        </h3>

                    </div>


                    <div
                        class="
                            w-11
                            h-11
                            rounded-xl
                            bg-green-50
                            flex
                            items-center
                            justify-center
                            text-xl
                        "
                    >

                        ✓

                    </div>

                </div>


                <p
                    class="
                        text-xs
                        text-green-600
                        mt-4
                    "
                >

                    Pelayanan telah selesai.

                </p>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- MAIN GRID -->
        <!-- ================================================= -->

        <div
            class="
                grid
                grid-cols-1
                xl:grid-cols-3
                gap-6
                mt-6
            "
        >


            <!-- ================================================= -->
            <!-- ANTRIAN -->
            <!-- ================================================= -->

            <div
                class="
                    xl:col-span-2
                    bg-white
                    rounded-2xl
                    border
                    border-slate-200
                    overflow-hidden
                "
            >

                <div
                    class="
                        p-5
                        border-b
                        border-slate-200
                        flex
                        items-center
                        justify-between
                    "
                >

                    <div>

                        <h3
                            class="
                                font-semibold
                                text-slate-800
                            "
                        >

                            Antrian Saat Ini

                        </h3>


                        <p
                            class="
                                text-sm
                                text-slate-500
                                mt-1
                            "
                        >

                            Status pelayanan pasien hari ini.

                        </p>

                    </div>


                    <router-link
                        to="/antrian"
                        class="
                            text-sm
                            text-blue-600
                            hover:text-blue-700
                            font-medium
                        "
                    >

                        Lihat Semua

                    </router-link>

                </div>


                <!-- LOADING -->

                <div
                    v-if="loadingDashboard"
                    class="
                        p-10
                        text-center
                        text-slate-400
                        text-sm
                    "
                >

                    Memuat data antrean...

                </div>


                <!-- EMPTY -->

                <div
                    v-else-if="antrian.length === 0"
                    class="
                        p-10
                        text-center
                    "
                >

                    <div
                        class="
                            text-4xl
                            mb-3
                        "
                    >

                        🎫

                    </div>


                    <p
                        class="
                            font-semibold
                            text-slate-700
                        "
                    >

                        Tidak ada antrean

                    </p>


                    <p
                        class="
                            text-sm
                            text-slate-500
                            mt-1
                        "
                    >

                        Belum ada pasien yang terdaftar hari ini.

                    </p>

                </div>


                <!-- TABLE -->

                <div
                    v-else
                    class="overflow-x-auto"
                >

                    <table
                        class="w-full text-sm"
                    >

                        <thead
                            class="
                                bg-slate-50
                                border-b
                                border-slate-200
                            "
                        >

                            <tr>

                                <th
                                    class="
                                        text-left
                                        px-5
                                        py-3
                                        font-medium
                                        text-slate-500
                                    "
                                >
                                    Antrian
                                </th>

                                <th
                                    class="
                                        text-left
                                        px-5
                                        py-3
                                        font-medium
                                        text-slate-500
                                    "
                                >
                                    Pasien
                                </th>

                                <th
                                    class="
                                        text-left
                                        px-5
                                        py-3
                                        font-medium
                                        text-slate-500
                                    "
                                >
                                    Poli
                                </th>

                                <th
                                    class="
                                        text-left
                                        px-5
                                        py-3
                                        font-medium
                                        text-slate-500
                                    "
                                >
                                    Dokter
                                </th>

                                <th
                                    class="
                                        text-left
                                        px-5
                                        py-3
                                        font-medium
                                        text-slate-500
                                    "
                                >
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-for="item in antrian"
                                :key="item.id"
                                class="
                                    border-t
                                    border-slate-100
                                    hover:bg-slate-50
                                "
                            >

                                <td
                                    class="
                                        px-5
                                        py-4
                                        font-semibold
                                        text-slate-800
                                    "
                                >

                                    {{ item.no_antrian || '-' }}

                                </td>


                                <td
                                    class="
                                        px-5
                                        py-4
                                        text-slate-700
                                    "
                                >

                                    {{
                                        item.pasien?.nama ||
                                        '-'
                                    }}

                                </td>


                                <td
                                    class="
                                        px-5
                                        py-4
                                        text-slate-600
                                    "
                                >

                                    {{
                                        item.poli?.nama ||
                                        '-'
                                    }}

                                </td>


                                <td
                                    class="
                                        px-5
                                        py-4
                                        text-slate-600
                                    "
                                >

                                    {{
                                        item.dokter?.nama ||
                                        '-'
                                    }}

                                </td>


                                <td
                                    class="
                                        px-5
                                        py-4
                                    "
                                >

                                    <span
                                        class="
                                            px-3
                                            py-1
                                            rounded-full
                                            text-xs
                                            font-medium
                                        "
                                        :class="
                                            statusClass(
                                                item.status
                                            )
                                        "
                                    >

                                        {{
                                            statusLabel(
                                                item.status
                                            )
                                        }}

                                    </span>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- POLI -->
            <!-- ================================================= -->

            <div
                class="
                    bg-white
                    rounded-2xl
                    border
                    border-slate-200
                    overflow-hidden
                "
            >

                <div
                    class="
                        p-5
                        border-b
                        border-slate-200
                    "
                >

                    <h3
                        class="
                            font-semibold
                            text-slate-800
                        "
                    >

                        Pasien Berdasarkan Poli

                    </h3>


                    <p
                        class="
                            text-sm
                            text-slate-500
                            mt-1
                        "
                    >

                        Distribusi pasien hari ini.

                    </p>

                </div>


                <div
                    v-if="poli.length === 0"
                    class="
                        p-8
                        text-center
                    "
                >

                    <div
                        class="
                            text-3xl
                            mb-2
                        "
                    >

                        🏥

                    </div>


                    <p
                        class="
                            text-sm
                            text-slate-500
                        "
                    >

                        Belum ada data poli.

                    </p>

                </div>


                <div
                    v-else
                    class="
                        p-5
                        space-y-4
                    "
                >

                    <div
                        v-for="item in poli"
                        :key="item.nama"
                    >

                        <div
                            class="
                                flex
                                items-center
                                justify-between
                                mb-2
                            "
                        >

                            <span
                                class="
                                    text-sm
                                    font-medium
                                    text-slate-700
                                "
                            >

                                {{ item.nama }}

                            </span>


                            <span
                                class="
                                    text-sm
                                    font-bold
                                    text-slate-800
                                "
                            >

                                {{ item.jumlah }}

                            </span>

                        </div>


                        <div
                            class="
                                h-2
                                bg-slate-100
                                rounded-full
                                overflow-hidden
                            "
                        >

                            <div
                                class="
                                    h-full
                                    bg-blue-600
                                    rounded-full
                                "
                                :style="{
                                    width:
                                        `${Math.min(
                                            100,
                                            (
                                                item.jumlah /
                                                Math.max(
                                                    statistics.pasien_hari_ini,
                                                    1
                                                )
                                            ) * 100
                                        )}%`
                                }"
                            ></div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- AKTIVITAS -->
        <!-- ================================================= -->

        <div
            class="
                mt-6
                bg-white
                rounded-2xl
                border
                border-slate-200
                overflow-hidden
            "
        >

            <div
                class="
                    p-5
                    border-b
                    border-slate-200
                "
            >

                <h3
                    class="
                        font-semibold
                        text-slate-800
                    "
                >

                    Aktivitas Pelayanan Terbaru

                </h3>


                <p
                    class="
                        text-sm
                        text-slate-500
                        mt-1
                    "
                >

                    Aktivitas pasien terbaru hari ini.

                </p>

            </div>


            <div
                v-if="aktivitas.length === 0"
                class="
                    p-10
                    text-center
                "
            >

                <div
                    class="
                        text-4xl
                        mb-3
                    "
                >

                    📋

                </div>


                <p
                    class="
                        font-semibold
                        text-slate-700
                    "
                >

                    Belum ada aktivitas

                </p>


                <p
                    class="
                        text-sm
                        text-slate-500
                        mt-1
                    "
                >

                    Aktivitas pelayanan akan muncul di sini.

                </p>

            </div>


            <div
                v-else
                class="
                    divide-y
                    divide-slate-100
                "
            >

                <div
                    v-for="item in aktivitas"
                    :key="item.id"
                    class="
                        px-5
                        py-4
                        flex
                        items-center
                        justify-between
                        gap-4
                        hover:bg-slate-50
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            gap-3
                        "
                    >

                        <div
                            class="
                                w-10
                                h-10
                                rounded-full
                                bg-blue-50
                                flex
                                items-center
                                justify-center
                            "
                        >

                            🧑

                        </div>


                        <div>

                            <p
                                class="
                                    text-sm
                                    font-semibold
                                    text-slate-800
                                "
                            >

                                {{ item.pasien }}

                            </p>


                            <p
                                class="
                                    text-xs
                                    text-slate-500
                                    mt-1
                                "
                            >

                                {{ item.poli }}

                                ·

                                Dr. {{ item.dokter }}

                            </p>

                        </div>

                    </div>


                    <span
                        class="
                            px-3
                            py-1
                            rounded-full
                            text-xs
                            font-medium
                            whitespace-nowrap
                        "
                        :class="
                            statusClass(
                                item.status
                            )
                        "
                    >

                        {{
                            statusLabel(
                                item.status
                            )
                        }}

                    </span>

                </div>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- QUICK ACCESS -->
        <!-- ================================================= -->

        <div
            class="
                mt-6
                grid
                grid-cols-2
                md:grid-cols-4
                gap-4
            "
        >

            <router-link
                to="/pasiens"
                class="
                    bg-white
                    border
                    border-slate-200
                    rounded-xl
                    p-4
                    hover:border-blue-300
                    hover:shadow-sm
                    transition
                "
            >

                <div
                    class="
                        text-xl
                        mb-2
                    "
                >

                    🧑

                </div>


                <p
                    class="
                        text-sm
                        font-semibold
                        text-slate-800
                    "
                >

                    Data Pasien

                </p>


                <p
                    class="
                        text-xs
                        text-slate-500
                        mt-1
                    "
                >

                    Kelola pasien

                </p>

            </router-link>


            <router-link
                to="/pendaftaran"
                class="
                    bg-white
                    border
                    border-slate-200
                    rounded-xl
                    p-4
                    hover:border-blue-300
                    hover:shadow-sm
                    transition
                "
            >

                <div
                    class="
                        text-xl
                        mb-2
                    "
                >

                    📝

                </div>


                <p
                    class="
                        text-sm
                        font-semibold
                        text-slate-800
                    "
                >

                    Pendaftaran

                </p>


                <p
                    class="
                        text-xs
                        text-slate-500
                        mt-1
                    "
                >

                    Daftarkan pasien

                </p>

            </router-link>


            <router-link
                to="/antrian"
                class="
                    bg-white
                    border
                    border-slate-200
                    rounded-xl
                    p-4
                    hover:border-blue-300
                    hover:shadow-sm
                    transition
                "
            >

                <div
                    class="
                        text-xl
                        mb-2
                    "
                >

                    🎫

                </div>


                <p
                    class="
                        text-sm
                        font-semibold
                        text-slate-800
                    "
                >

                    Antrian

                </p>


                <p
                    class="
                        text-xs
                        text-slate-500
                        mt-1
                    "
                >

                    Pantau antrian

                </p>

            </router-link>


            <router-link
                to="/pemeriksaan"
                class="
                    bg-white
                    border
                    border-slate-200
                    rounded-xl
                    p-4
                    hover:border-blue-300
                    hover:shadow-sm
                    transition
                "
            >

                <div
                    class="
                        text-xl
                        mb-2
                    "
                >

                    🩺

                </div>


                <p
                    class="
                        text-sm
                        font-semibold
                        text-slate-800
                    "
                >

                    Pemeriksaan

                </p>


                <p
                    class="
                        text-xs
                        text-slate-500
                        mt-1
                    "
                >

                    Pelayanan pasien

                </p>

            </router-link>

        </div>

    </main>

</div>

</template>
```

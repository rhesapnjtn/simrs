<script setup>

import {
    ref,
    computed,
    onMounted
} from 'vue';

import {
    useRoute,
    useRouter
} from 'vue-router';

import axios from 'axios';


// =====================================================
// ROUTER
// =====================================================

const route = useRoute();
const router = useRouter();


// =====================================================
// USER
// =====================================================

const user = ref(null);
const userLoading = ref(true);


// =====================================================
// MOBILE SIDEBAR
// =====================================================

const sidebarOpen = ref(false);


// =====================================================
// USER NAME
// =====================================================

const userName = computed(() => {

    return user.value?.name || 'User';

});


// =====================================================
// USER INITIAL
// =====================================================

const userInitial = computed(() => {

    return userName.value
        .charAt(0)
        .toUpperCase();

});


// =====================================================
// ROLES
// =====================================================

const roles = computed(() => {

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


// =====================================================
// USER ROLE
// =====================================================

const userRole = computed(() => {

    if (roles.value.length === 0) {
        return 'USER';
    }

    return roles.value[0];

});


// =====================================================
// SUPER ADMIN
// =====================================================

const isSuperAdmin = computed(() => {

    return roles.value.includes('SUPER_ADMIN');

});


// =====================================================
// CHECK ROLE
// =====================================================

const hasRole = (role) => {

    if (!role) {
        return false;
    }

    return roles.value.includes(
        role.toUpperCase()
    );

};


// =====================================================
// GET USER
// =====================================================

const getUser = async () => {

    try {

        userLoading.value = true;

        const response = await axios.get(
            '/api/user'
        );

        user.value =
            response.data.user ||
            response.data;


        console.log(
            '================================'
        );

        console.log(
            'APP LAYOUT USER:',
            user.value
        );

        console.log(
            'APP LAYOUT ROLES:',
            roles.value
        );

        console.log(
            'APP LAYOUT IS SUPER ADMIN:',
            isSuperAdmin.value
        );

        console.log(
            '================================'
        );


    } catch (error) {

        console.error(
            'Gagal mengambil data user:',
            error
        );

        user.value = null;

    } finally {

        userLoading.value = false;

    }

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
// ACTIVE MENU
// =====================================================

const isActive = (path) => {

    return (
        route.path === path ||
        route.path.startsWith(path + '/')
    );

};


// =====================================================
// CLOSE SIDEBAR
// =====================================================

const closeSidebar = () => {

    sidebarOpen.value = false;

};


// =====================================================
// NAVIGATE MOBILE
// =====================================================

const navigateMobile = () => {

    if (window.innerWidth < 1024) {

        sidebarOpen.value = false;

    }

};


// =====================================================
// MOUNTED
// =====================================================

onMounted(() => {

    getUser();

});

</script>


<template>

<div class="min-h-screen bg-gray-100">


    <!-- ================================================= -->
    <!-- MOBILE OVERLAY -->
    <!-- ================================================= -->

    <Transition name="fade">

        <div
            v-if="sidebarOpen"
            class="
                fixed
                inset-0
                z-40
                bg-black/40
                backdrop-blur-[1px]
                lg:hidden
            "
            @click="closeSidebar"
        ></div>

    </Transition>


    <!-- ================================================= -->
    <!-- SIDEBAR -->
    <!-- ================================================= -->

    <Transition name="sidebar">

        <aside
            v-show="
                sidebarOpen ||
                true
            "
            class="
                fixed
                inset-y-0
                left-0
                z-50
                w-72
                bg-white
                border-r
                border-gray-200
                flex
                flex-col

                transform
                transition-transform
                duration-300
                ease-in-out

                -translate-x-full
                lg:translate-x-0

                shadow-xl
                lg:shadow-none
            "
            :class="{
                'translate-x-0': sidebarOpen
            }"
        >


            <!-- ================================================= -->
            <!-- LOGO -->
            <!-- ================================================= -->

            <div
                class="
                    flex
                    items-center
                    justify-between
                    gap-3
                    h-16
                    px-5
                    border-b
                    border-gray-200
                    flex-shrink-0
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
                            flex
                            items-center
                            justify-center
                            w-10
                            h-10
                            rounded-lg
                            bg-blue-600
                            text-white
                            font-bold
                            text-lg
                        "
                    >

                        S

                    </div>


                    <div>

                        <h1
                            class="
                                font-bold
                                text-gray-800
                            "
                        >

                            SIMRS

                        </h1>


                        <p
                            class="
                                text-[10px]
                                text-gray-500
                            "
                        >

                            Sistem Informasi Rumah Sakit

                        </p>

                    </div>

                </div>


                <!-- CLOSE MOBILE -->

                <button
                    @click="closeSidebar"
                    class="
                        lg:hidden
                        w-9
                        h-9
                        flex
                        items-center
                        justify-center
                        rounded-lg
                        text-gray-500
                        hover:bg-gray-100
                        hover:text-gray-800
                        transition
                    "
                    aria-label="Tutup menu"
                >

                    ✕

                </button>

            </div>


            <!-- ================================================= -->
            <!-- MENU -->
            <!-- ================================================= -->

            <nav
                v-if="!userLoading"
                class="
                    flex-1
                    overflow-y-auto
                    p-4
                    space-y-1
                    min-h-0
                "
            >


                <!-- ================================================= -->
                <!-- DASHBOARD -->
                <!-- ================================================= -->

                <router-link
                    to="/dashboard"
                    class="sidebar-item"
                    :class="{
                        active: isActive('/dashboard')
                    }"
                    @click="navigateMobile"
                >

                    <span class="menu-icon">
                        🏠
                    </span>

                    <span>
                        Dashboard
                    </span>

                </router-link>


                <!-- ================================================= -->
                <!-- ADMINISTRASI -->
                <!-- ================================================= -->

                <template
                    v-if="
                        isSuperAdmin ||
                        hasRole('ADMIN')
                    "
                >

                    <div class="menu-title">
                        ADMINISTRASI
                    </div>


                    <!-- USERS -->

                    <router-link
                        v-if="isSuperAdmin"
                        to="/users"
                        class="sidebar-item"
                        :class="{
                            active: isActive('/users')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            👥
                        </span>

                        <span>
                            Users
                        </span>

                    </router-link>


                    <!-- PASIEN -->

                    <router-link
                        to="/pasiens"
                        class="sidebar-item"
                        :class="{
                            active: isActive('/pasiens')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            🧑
                        </span>

                        <span>
                            Pasien
                        </span>

                    </router-link>


                    <!-- PENDAFTARAN -->

                    <router-link
                        to="/pendaftaran"
                        class="sidebar-item"
                        :class="{
                            active: isActive('/pendaftaran')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            📝
                        </span>

                        <span>
                            Pendaftaran
                        </span>

                    </router-link>


                    <!-- ANTRIAN -->

                    <router-link
                        to="/antrian"
                        class="sidebar-item"
                        :class="{
                            active: isActive('/antrian')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            🎫
                        </span>

                        <span>
                            Antrian
                        </span>

                    </router-link>

                </template>


                <!-- ================================================= -->
                <!-- MASTER DATA -->
                <!-- ================================================= -->

                <template
                    v-if="isSuperAdmin"
                >

                    <div class="menu-title">
                        MASTER DATA
                    </div>


                    <!-- POLI -->

                    <router-link
                        to="/polis"
                        class="sidebar-item"
                        :class="{
                            active: isActive('/polis')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            🏥
                        </span>

                        <span>
                            Poli
                        </span>

                    </router-link>


                    <!-- DOKTER -->

                    <router-link
                        to="/dokters"
                        class="sidebar-item"
                        :class="{
                            active: isActive('/dokters')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            👨‍⚕️
                        </span>

                        <span>
                            Dokter
                        </span>

                    </router-link>

                </template>


                <!-- ================================================= -->
                <!-- PELAYANAN DOKTER -->
                <!-- ================================================= -->

                <template
                    v-if="
                        isSuperAdmin ||
                        hasRole('DOKTER')
                    "
                >

                    <div class="menu-title">
                        PELAYANAN DOKTER
                    </div>


                    <!-- PEMERIKSAAN -->

                    <router-link
                        to="/pemeriksaan"
                        class="sidebar-item"
                        :class="{
                            active: isActive('/pemeriksaan')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            🩺
                        </span>

                        <span>
                            Pemeriksaan
                        </span>

                    </router-link>


                    <!-- RESEP -->

                    <router-link
                        to="/resep"
                        class="sidebar-item"
                        :class="{
                            active: isActive('/resep')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            💊
                        </span>

                        <span>
                            Resep
                        </span>

                    </router-link>


                    <!-- RIWAYAT -->

                    <router-link
                        to="/riwayat-pemeriksaan"
                        class="sidebar-item"
                        :class="{
                            active:
                                isActive(
                                    '/riwayat-pemeriksaan'
                                )
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            📋
                        </span>

                        <span>
                            Riwayat Pemeriksaan
                        </span>

                    </router-link>

                </template>


                <!-- ================================================= -->
                <!-- PELAYANAN PERAWAT -->
                <!-- ================================================= -->

                <template
                    v-if="
                        isSuperAdmin ||
                        hasRole('PERAWAT')
                    "
                >

                    <div class="menu-title">
                        PELAYANAN
                    </div>


                    <!-- PASIEN -->

                    <router-link
                        to="/pasiens"
                        class="sidebar-item"
                        :class="{
                            active:
                                isActive('/pasiens')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            🧑‍⚕️
                        </span>

                        <span>
                            Pasien
                        </span>

                    </router-link>


                    <!-- ANTRIAN -->

                    <router-link
                        to="/antrian"
                        class="sidebar-item"
                        :class="{
                            active:
                                isActive('/antrian')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            🎫
                        </span>

                        <span>
                            Antrian
                        </span>

                    </router-link>


                    <!-- PEMERIKSAAN -->

                    <router-link
                        to="/pemeriksaan"
                        class="sidebar-item"
                        :class="{
                            active:
                                isActive('/pemeriksaan')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            🩺
                        </span>

                        <span>
                            Pemeriksaan
                        </span>

                    </router-link>

                </template>


                <!-- ================================================= -->
                <!-- FARMASI -->
                <!-- ================================================= -->

                <template
                    v-if="
                        isSuperAdmin ||
                        hasRole('FARMASI')
                    "
                >

                    <div class="menu-title">
                        FARMASI
                    </div>


                    <!-- DATA OBAT -->

                    <router-link
                        to="/obat"
                        class="sidebar-item"
                        :class="{
                            active:
                                isActive('/obat')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            💊
                        </span>

                        <span>
                            Data Obat
                        </span>

                    </router-link>


                    <!-- APOTEK -->

                    <router-link
                        to="/apotek"
                        class="sidebar-item"
                        :class="{
                            active:
                                isActive('/apotek')
                        }"
                        @click="navigateMobile"
                    >

                        <span class="menu-icon">
                            🏪
                        </span>

                        <span>
                            Apotek
                        </span>

                    </router-link>

                </template>

            </nav>


            <!-- ================================================= -->
            <!-- LOADING -->
            <!-- ================================================= -->

            <div
                v-else
                class="
                    flex-1
                    flex
                    items-center
                    justify-center
                    text-sm
                    text-gray-400
                "
            >

                Memuat menu...

            </div>


            <!-- ================================================= -->
            <!-- USER -->
            <!-- ================================================= -->

            <div
                class="
                    flex-shrink-0
                    border-t
                    border-gray-200
                    bg-white
                    p-4
                "
            >

                <div
                    class="
                        flex
                        items-center
                        gap-3
                        mb-3
                    "
                >

                    <!-- INITIAL -->

                    <div
                        class="
                            flex
                            items-center
                            justify-center
                            w-10
                            h-10
                            rounded-full
                            bg-blue-100
                            font-semibold
                            text-blue-700
                            flex-shrink-0
                        "
                    >

                        {{ userInitial }}

                    </div>


                    <!-- USER INFO -->

                    <div
                        class="min-w-0 flex-1"
                    >

                        <p
                            class="
                                text-sm
                                font-semibold
                                text-gray-800
                                truncate
                            "
                        >

                            {{ userName }}

                        </p>


                        <p
                            class="
                                text-xs
                                text-gray-500
                                truncate
                            "
                        >

                            {{ userRole }}

                        </p>

                    </div>

                </div>


                <!-- LOGOUT -->

                <button
                    @click="logout"
                    class="
                        w-full
                        flex
                        items-center
                        justify-center
                        gap-2
                        px-3
                        py-2.5
                        text-sm
                        text-red-600
                        hover:bg-red-50
                        rounded-lg
                        transition
                    "
                >

                    <span>
                        🚪
                    </span>

                    <span>
                        Logout
                    </span>

                </button>

            </div>

        </aside>

    </Transition>


    <!-- ================================================= -->
    <!-- MAIN -->
    <!-- ================================================= -->

    <main
        class="
            min-h-screen
            lg:ml-72
        "
    >


        <!-- ================================================= -->
        <!-- TOPBAR -->
        <!-- ================================================= -->

        <header
            class="
                sticky
                top-0
                z-30
                h-16
                bg-white
                border-b
                border-gray-200
                flex
                items-center
                justify-between
                px-4
                sm:px-6
            "
        >


            <!-- LEFT -->

            <div
                class="
                    flex
                    items-center
                    gap-3
                "
            >

                <!-- HAMBURGER -->

                <button
                    @click="sidebarOpen = true"
                    class="
                        lg:hidden
                        w-10
                        h-10
                        flex
                        items-center
                        justify-center
                        rounded-lg
                        text-gray-600
                        hover:bg-gray-100
                        transition
                    "
                    aria-label="Buka menu"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                    </svg>

                </button>


                <!-- TITLE -->

                <div>

                    <h2
                        class="
                            font-semibold
                            text-gray-800
                            text-sm
                            sm:text-base
                        "
                    >

                        Sistem Informasi Rumah Sakit

                    </h2>


                    <p
                        class="
                            hidden
                            sm:block
                            text-xs
                            text-gray-500
                        "
                    >

                        Hospital Management System

                    </p>

                </div>

            </div>


            <!-- USER TOPBAR -->

            <div
                class="
                    flex
                    items-center
                    gap-3
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
                            text-gray-800
                        "
                    >

                        {{ userName }}

                    </p>


                    <p
                        class="
                            text-xs
                            text-gray-500
                        "
                    >

                        {{ userRole }}

                    </p>

                </div>


                <!-- AVATAR -->

                <div
                    class="
                        flex
                        items-center
                        justify-center
                        w-9
                        h-9
                        rounded-full
                        bg-blue-100
                        text-blue-700
                        font-bold
                    "
                >

                    {{ userInitial }}

                </div>

            </div>

        </header>


        <!-- ================================================= -->
        <!-- PAGE CONTENT -->
        <!-- ================================================= -->

        <section
            class="
                p-4
                sm:p-5
                lg:p-6
            "
        >

            <router-view />

        </section>

    </main>

</div>

</template>


<style scoped>


/* ===================================================== */
/* SIDEBAR ITEM */
/* ===================================================== */

.sidebar-item {

    display: flex;

    align-items: center;

    gap: 12px;

    width: 100%;

    padding: 10px 12px;

    border-radius: 8px;

    color: #4b5563;

    font-size: 14px;

    font-weight: 500;

    transition:
        background-color 0.2s ease,
        color 0.2s ease,
        transform 0.15s ease;

}


.sidebar-item:hover {

    background-color: #f3f4f6;

    color: #111827;

}


.sidebar-item:active {

    transform: scale(0.98);

}


.sidebar-item.active {

    background-color: #eff6ff;

    color: #2563eb;

}


.menu-icon {

    width: 24px;

    min-width: 24px;

    text-align: center;

    font-size: 17px;

}


/* ===================================================== */
/* MENU TITLE */
/* ===================================================== */

.menu-title {

    padding:
        18px
        12px
        7px;

    font-size: 10px;

    font-weight: 700;

    color: #9ca3af;

    letter-spacing:
        0.08em;

}


/* ===================================================== */
/* SCROLLBAR */
/* ===================================================== */

nav::-webkit-scrollbar {

    width: 5px;

}


nav::-webkit-scrollbar-track {

    background: transparent;

}


nav::-webkit-scrollbar-thumb {

    background: #d1d5db;

    border-radius: 999px;

}


nav::-webkit-scrollbar-thumb:hover {

    background: #9ca3af;

}


/* ===================================================== */
/* SIDEBAR TRANSITION */
/* ===================================================== */

.sidebar-enter-active,
.sidebar-leave-active {

    transition:
        transform 0.3s ease;

}


.sidebar-enter-from,
.sidebar-leave-to {

    transform: translateX(-100%);

}


/* ===================================================== */
/* OVERLAY TRANSITION */
/* ===================================================== */

.fade-enter-active,
.fade-leave-active {

    transition:
        opacity 0.2s ease;

}


.fade-enter-from,
.fade-leave-to {

    opacity: 0;

}


/* ===================================================== */
/* MOBILE */
/* ===================================================== */

@media (max-width: 1023px) {

    .sidebar-item {

        padding:
            12px
            14px;

    }

}


/* ===================================================== */
/* LARGE SCREEN */
/* ===================================================== */

@media (min-width: 1024px) {

    aside {

        transform: translateX(0) !important;

    }

}

</style>
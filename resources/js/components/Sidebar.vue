<template>
    <aside
        class="fixed left-0 top-0 z-40 h-screen w-64 border-r border-gray-200 bg-white"
    >
        <!-- Logo / Brand -->
        <div class="flex h-16 items-center border-b border-gray-200 px-6">
            <div>
                <h1 class="text-xl font-bold text-blue-600">
                    SIMRS
                </h1>

                <p class="text-xs text-gray-500">
                    Sistem Informasi Rumah Sakit
                </p>
            </div>
        </div>


        <!-- Menu -->
        <nav class="h-[calc(100vh-4rem)] overflow-y-auto px-3 py-5">

            <!-- DASHBOARD -->
            <div class="mb-6">
                <p
                    class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400"
                >
                    Utama
                </p>

                <router-link
                    to="/dashboard"
                    class="menu-item"
                    :class="{ 'menu-active': isActive('/dashboard') }"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"
                        />
                    </svg>

                    <span>Dashboard</span>
                </router-link>
            </div>


            <!-- MASTER DATA -->
            <div class="mb-6">
                <p
                    class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400"
                >
                    Master Data
                </p>


                <!-- Pasien -->
                <router-link
                    to="/pasiens"
                    class="menu-item"
                    :class="{ 'menu-active': isActive('/pasiens') }"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6 3 3 0 000 6z"
                        />
                    </svg>

                    <span>Pasien</span>
                </router-link>


                <!-- Dokter -->
                <router-link
                    to="/dokters"
                    class="menu-item"
                    :class="{ 'menu-active': isActive('/dokters') }"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>

                    <span>Dokter</span>
                </router-link>


                <!-- Poli -->
                <router-link
                    to="/polis"
                    class="menu-item"
                    :class="{ 'menu-active': isActive('/polis') }"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 14a7 7 0 01-14 0m7-7V3m0 0L9 6m3-3l3 3"
                        />
                    </svg>

                    <span>Poli</span>
                </router-link>


                <!-- Obat -->
                <router-link
                    to="/obat"
                    class="menu-item"
                    :class="{ 'menu-active': isActive('/obat') }"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"
                        />
                    </svg>

                    <span>Obat</span>
                </router-link>
            </div>


            <!-- PELAYANAN -->
            <div class="mb-6">
                <p
                    class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400"
                >
                    Pelayanan
                </p>


                <!-- Pendaftaran -->
                <router-link
                    to="/pendaftaran"
                    class="menu-item"
                    :class="{ 'menu-active': isActive('/pendaftaran') }"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A2 2 0 0114 3.586L18.414 8A2 2 0 0119 9.414V19a2 2 0 01-2 2z"
                        />
                    </svg>

                    <span>Pendaftaran</span>
                </router-link>


                <!-- Antrian -->
                <router-link
                    to="/antrian"
                    class="menu-item"
                    :class="{ 'menu-active': isActive('/antrian') }"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7h8M8 11h8M8 15h5M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"
                        />
                    </svg>

                    <span>Antrian</span>
                </router-link>


                <!-- Pemeriksaan -->
                <router-link
                    to="/pendaftaran"
                    class="menu-item"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-3-3v6m8-3a8 8 0 11-16 0 8 8 0 0116 0z"
                        />
                    </svg>

                    <span>Pemeriksaan</span>
                </router-link>


                <!-- Resep -->
                <router-link
                    to="/resep"
                    class="menu-item"
                    :class="{ 'menu-active': isActive('/resep') }"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A2 2 0 0114 3.586L18.414 8A2 2 0 0119 9.414V19a2 2 0 01-2 2z"
                        />
                    </svg>

                    <span>Resep</span>
                </router-link>


                <!-- Apotek -->
                <router-link
                    to="/apotek"
                    class="menu-item"
                    :class="{ 'menu-active': isActive('/apotek') }"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19.5 12.5l-7-7a2.121 2.121 0 00-3 0l-5 5a2.121 2.121 0 000 3l7 7a2.121 2.121 0 003 0l5-5a2.121 2.121 0 000-3zM9 9l6 6m-3-3h.01"
                        />
                    </svg>

                    <span>Apotek</span>
                </router-link>


                <!-- Riwayat Pasien -->
                <router-link
                    to="/pasiens"
                    class="menu-item"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                    <span>Riwayat Pasien</span>
                </router-link>
            </div>


            <!-- ADMIN -->
            <div class="mb-6">
                <p
                    class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400"
                >
                    Administrasi
                </p>

                <router-link
                    to="/users"
                    class="menu-item"
                    :class="{ 'menu-active': isActive('/users') }"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm6 0h-6v-1a6 6 0 0112 0v1zm-3-11a3 3 0 100-6 3 3 0 000 6z"
                        />
                    </svg>

                    <span>User Management</span>
                </router-link>
            </div>

        </nav>
    </aside>
</template>


<script setup>
import { useRoute } from 'vue-router';

const route = useRoute();

const isActive = (path) => {
    return route.path === path || route.path.startsWith(path + '/');
};
</script>


<style scoped>
.menu-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    padding: 0.65rem 0.75rem;
    margin-bottom: 0.25rem;
    border-radius: 0.5rem;
    color: #4b5563;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.menu-item:hover {
    background-color: #f3f4f6;
    color: #2563eb;
}

.menu-active {
    background-color: #eff6ff;
    color: #2563eb;
    font-weight: 600;
}
</style>
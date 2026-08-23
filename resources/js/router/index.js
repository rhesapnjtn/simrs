import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';

// =====================================================
// LAYOUT
// =====================================================

import AppLayout from '../layouts/AppLayout.vue';

// =====================================================
// PAGES
// =====================================================

import Login from '../pages/Login.vue';
import Dashboard from '../pages/Dashboard.vue';

import Users from '../pages/Users.vue';
import CreateUser from '../pages/CreateUser.vue';
import EditUser from '../pages/EditUser.vue';

import Polis from '../pages/Polis.vue';
import CreatePoli from '../pages/CreatePoli.vue';
import EditPoli from '../pages/EditPoli.vue';

import Dokters from '../pages/Dokters.vue';
import CreateDokter from '../pages/CreateDokter.vue';
import EditDokter from '../pages/EditDokter.vue';

import Pasiens from '../pages/Pasiens.vue';
import CreatePasien from '../pages/CreatePasien.vue';
import EditPasien from '../pages/EditPasien.vue';

import Pendaftaran from '../pages/Pendaftaran.vue';
import Antrian from '../pages/Antrian.vue';

import PemeriksaanPage from '../pages/PemeriksaanPage.vue';
import RiwayatPemeriksaan from '../pages/RiwayatPemeriksaan.vue';
import RiwayatPasien from '../pages/RiwayatPasien.vue';

import ObatManagement from '../pages/ObatManagement.vue';
import ResepPage from '../pages/ResepPage.vue';

import LabPemeriksaan from '../pages/LabPemeriksaan.vue';
import Laboratorium from '../pages/Laboratorium.vue';

import Apotek from '../pages/Apotek.vue';
import DokterRiwayatPasien from '../pages/DokterRiwayatPasien.vue';


// =====================================================
// ROUTES
// =====================================================

const routes = [

    // =====================================================
    // LOGIN
    // =====================================================

    {
        path: '/login',
        name: 'login',
        component: Login,

        meta: {
            guestOnly: true,
        },
    },


    // =====================================================
    // APP LAYOUT
    // SEMUA HALAMAN SISTEM ADA DI DALAM SINI
    // =====================================================

    {
        path: '/',
        component: AppLayout,

        meta: {
            requiresAuth: true,
        },

        children: [

            // =================================================
            // DASHBOARD
            // =================================================

            {
                path: 'dashboard',
                name: 'dashboard',
                component: Dashboard,

                meta: {
                    requiresAuth: true,
                },
            },


            // =================================================
            // USERS
            // =================================================

            {
                path: 'users',
                name: 'users',
                component: Users,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                    ],
                },
            },

            {
                path: 'users/create',
                name: 'users.create',
                component: CreateUser,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                    ],
                },
            },

            {
                path: 'users/:id/edit',
                name: 'users.edit',
                component: EditUser,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                    ],
                },
            },


            // =================================================
            // POLI
            // =================================================

            {
                path: 'polis',
                name: 'polis',
                component: Polis,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                    ],
                },
            },

            {
                path: 'polis/create',
                name: 'polis.create',
                component: CreatePoli,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                    ],
                },
            },

            {
                path: 'polis/:id/edit',
                name: 'polis.edit',
                component: EditPoli,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                    ],
                },
            },


            // =================================================
            // DOKTER
            // =================================================

            {
                path: 'dokters',
                name: 'dokters',
                component: Dokters,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                    ],
                },
            },

            {
                path: 'dokters/create',
                name: 'dokters.create',
                component: CreateDokter,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                    ],
                },
            },

            {
                path: 'dokters/:id/edit',
                name: 'dokters.edit',
                component: EditDokter,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                    ],
                },
            },


            // =================================================
            // PASIEN
            // =================================================

            {
                path: 'pasiens',
                name: 'pasiens',
                component: Pasiens,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                        'DOKTER',
                        'PERAWAT',
                        'LABORATORIUM',
                    ],
                },
            },

            {
                path: 'pasiens/create',
                name: 'pasiens.create',
                component: CreatePasien,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                    ],
                },
            },

            {
                path: 'pasiens/:id/edit',
                name: 'pasiens.edit',
                component: EditPasien,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                    ],
                },
            },


            // =================================================
            // PENDAFTARAN
            // =================================================

            {
                path: 'pendaftaran',
                name: 'pendaftaran',
                component: Pendaftaran,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                    ],
                },
            },


            // =================================================
            // ANTRIAN
            // =================================================

            {
                path: 'antrian',
                name: 'antrian',
                component: Antrian,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'ADMIN',
                        'DOKTER',
                        'PERAWAT',
                    ],
                },
            },


            // =================================================
            // PEMERIKSAAN
            // =================================================

            {
                path: 'pemeriksaan',
                name: 'pemeriksaan.index',
                component: PemeriksaanPage,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'DOKTER',
                        'PERAWAT',
                    ],
                },
            },

            {
                path: 'pendaftarans/:id/pemeriksaan',
                name: 'pemeriksaan.detail',
                component: PemeriksaanPage,

                props: true,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'DOKTER',
                        'PERAWAT',
                    ],
                },
            },


            // =================================================
            // RIWAYAT PEMERIKSAAN
            // =================================================

            {
                path: 'pasien/:pasienId/riwayat-pemeriksaan',
                name: 'RiwayatPemeriksaan',
                component: RiwayatPemeriksaan,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'DOKTER',
                        'PERAWAT',
                    ],
                },
            },

            {
                path: 'riwayat-pemeriksaan',
                name: 'riwayat-pemeriksaan',
                component: RiwayatPemeriksaan,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'DOKTER',
                        'PERAWAT',
                    ],
                },
            },

            {
                path: 'pasiens/:id/riwayat',
                name: 'riwayat-pasien',
                component: RiwayatPasien,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'DOKTER',
                        'PERAWAT',
                    ],
                },
            },


            // =================================================
            // OBAT
            // =================================================

            {
                path: 'obat',
                name: 'obat',
                component: ObatManagement,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'FARMASI',
                    ],
                },
            },


            // =================================================
            // RESEP
            // =================================================

            {
                path: 'resep',
                name: 'resep',
                component: ResepPage,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'DOKTER',
                    ],
                },
            },


            // =================================================
            // APOTEK
            // =================================================

            {
                path: 'apotek',
                name: 'apotek',
                component: Apotek,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'FARMASI',
                    ],
                },
            },


            // =================================================
            // LABORATORIUM
            // =================================================

            {
                path: 'lab-pemeriksaan',
                name: 'lab-pemeriksaan',
                component: LabPemeriksaan,

                meta: {
                    requiresAuth: true,

                    roles: [
                        'SUPER_ADMIN',
                        'LABORATORIUM',
                    ],
                },
            },

            {
                path: 'laboratorium',
                name: 'laboratorium',
                component: Laboratorium,

                meta: {
                    requiresAuth: true,

                    roles: [
                        'SUPER_ADMIN',
                        'LABORATORIUM',
                    ],
                },
            },


            // =================================================
            // DOKTER - RIWAYAT PASIEN
            // =================================================

            {
                path: 'dokter/riwayat-pasien',
                name: 'DokterRiwayatPasien',
                component: DokterRiwayatPasien,

                meta: {
                    roles: [
                        'SUPER_ADMIN',
                        'DOKTER',
                    ],
                },
            },

        ],
    },

];


// =====================================================
// CREATE ROUTER
// =====================================================

const router = createRouter({
    history: createWebHistory(),
    routes,

    scrollBehavior() {
        return {
            top: 0,
        };
    },
});


// =====================================================
// ROUTER GUARD
// =====================================================

router.beforeEach(async (to) => {

    console.log('================================');
    console.log('ROUTER GUARD');
    console.log('PATH:', to.path);
    console.log('================================');


    // =================================================
    // LOGIN
    // =================================================

    if (to.name === 'login') {

        try {

            const response = await axios.get(
                '/api/user',
                {
                    withCredentials: true,
                }
            );

            const user =
                response.data.user ||
                response.data;

            if (user) {
                return '/dashboard';
            }

        } catch (error) {

            return true;

        }
    }


    // =================================================
    // CEK AUTH
    // =================================================

    if (
        to.meta.requiresAuth ||
        to.meta.roles
    ) {

        let user;

        try {

            const response = await axios.get(
                '/api/user',
                {
                    withCredentials: true,
                }
            );

            user =
                response.data.user ||
                response.data;

        } catch (error) {

            console.error(
                'AUTH CHECK FAILED:',
                error.response?.status
            );

            return '/login';
        }


        // =================================================
        // AMBIL ROLE
        // =================================================

        let roles = [];

        if (Array.isArray(user?.roles)) {

            roles = user.roles
                .map((role) => {

                    if (
                        typeof role === 'string'
                    ) {
                        return role
                            .trim()
                            .toUpperCase();
                    }

                    return role?.name
                        ?.trim()
                        ?.toUpperCase();

                })
                .filter(Boolean);
        }


        console.log('USER:', user);
        console.log('ROLES:', roles);


        // =================================================
        // SUPER ADMIN = FULL ACCESS
        // =================================================

        if (
            roles.includes('SUPER_ADMIN')
        ) {

            console.log(
                'SUPER_ADMIN -> FULL ACCESS'
            );

            return true;
        }


        // =================================================
        // ROUTE TANPA ROLE
        // =================================================

        if (!to.meta.roles) {
            return true;
        }


        // =================================================
        // CEK ROLE
        // =================================================

        const requiredRoles =
            Array.isArray(to.meta.roles)
                ? to.meta.roles
                : [];


        const hasPermission =
            requiredRoles.some(
                (requiredRole) =>
                    roles.includes(
                        requiredRole
                            .trim()
                            .toUpperCase()
                    )
            );


        if (hasPermission) {

            console.log(
                'AKSES DIIJINKAN'
            );

            return true;
        }


        // =================================================
        // AKSES DITOLAK
        // =================================================

        console.warn(
            'AKSES DITOLAK',
            {
                userRoles: roles,
                requiredRoles,
                path: to.path,
            }
        );

        return '/dashboard';
    }


    return true;
});


export default router;
<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const email = ref('');
const password = ref('');

const loading = ref(false);
const errorMessage = ref('');

const login = async () => {
    loading.value = true;
    errorMessage.value = '';

    try {
        // Ambil CSRF cookie dari Laravel Sanctum
        await axios.get('/sanctum/csrf-cookie');

        // Login ke Laravel
        await axios.post('/api/login', {
            email: email.value,
            password: password.value,
        });

        // Hanya masuk dashboard kalau login berhasil
        router.push('/dashboard');

    } catch (error) {
        console.error(error);

        errorMessage.value =
            error.response?.data?.message ||
            'Email atau password salah.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-100 flex items-center justify-center px-4">

        <div class="w-full max-w-md">

            <!-- Logo -->
            <div class="text-center mb-8">

                <div
                    class="inline-flex items-center justify-center
                           w-16 h-16 rounded-2xl bg-blue-600
                           text-white text-2xl font-bold"
                >
                    S
                </div>

                <h1 class="mt-4 text-3xl font-bold text-slate-800">
                    SIMRS
                </h1>

                <p class="mt-2 text-slate-500">
                    Hospital Management System
                </p>

            </div>


            <!-- Login Card -->
            <div
                class="bg-white rounded-2xl shadow-sm
                       border border-slate-200 p-8"
            >

                <h2 class="text-xl font-semibold text-slate-800">
                    Sign In
                </h2>

                <p class="text-sm text-slate-500 mt-1 mb-6">
                    Masuk ke sistem rumah sakit
                </p>


                <!-- Error Message -->
                <div
                    v-if="errorMessage"
                    class="mb-5 p-3 rounded-xl
                           bg-red-50 border border-red-200
                           text-red-600 text-sm"
                >
                    {{ errorMessage }}
                </div>


                <form @submit.prevent="login">

                    <!-- Email -->
                    <div class="mb-4">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Email
                        </label>

                        <input
                            v-model="email"
                            type="email"
                            placeholder="superadmin@simrs.test"
                            required
                            autocomplete="email"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Password -->
                    <div class="mb-6">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Password
                        </label>

                        <input
                            v-model="password"
                            type="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Login Button -->
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 rounded-xl
                               bg-blue-600 text-white
                               font-semibold
                               hover:bg-blue-700
                               disabled:opacity-50
                               disabled:cursor-not-allowed
                               transition"
                    >
                        {{ loading ? 'Signing In...' : 'Sign In' }}
                    </button>

                </form>

            </div>

        </div>

    </div>
</template>
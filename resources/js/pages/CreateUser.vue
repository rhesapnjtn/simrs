<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const form = ref({
    name: '',
    email: '',
    password: '',
    role: '',
});

const roles = ref([]);
const loading = ref(false);
const loadingRoles = ref(true);
const error = ref('');
const success = ref('');

const getRoles = async () => {
    try {
        const response = await axios.get('/api/roles');

        roles.value = response.data.roles;

    } catch (err) {
        console.error(err);

        error.value = 'Gagal mengambil daftar role.';
    } finally {
        loadingRoles.value = false;
    }
};


const submit = async () => {

    error.value = '';
    success.value = '';

    if (
        !form.value.name ||
        !form.value.email ||
        !form.value.password ||
        !form.value.role
    ) {
        error.value = 'Semua field wajib diisi.';
        return;
    }

    try {

        loading.value = true;

        await axios.post('/api/users', form.value);

        success.value = 'User berhasil dibuat.';

        setTimeout(() => {
            router.push('/users');
        }, 800);

    } catch (err) {

        console.error(err);

        if (err.response?.status === 422) {

            const errors = err.response.data.errors;

            error.value = Object.values(errors)
                .flat()
                .join(' ');

        } else {

            error.value =
                err.response?.data?.message ||
                'Gagal membuat user.';

        }

    } finally {

        loading.value = false;

    }
};


const cancel = () => {
    router.push('/users');
};


onMounted(() => {
    getRoles();
});
</script>


<template>

    <div class="min-h-screen bg-slate-100">

        <!-- Navbar -->
        <header class="bg-white border-b border-slate-200">

            <div class="h-16 px-6 flex items-center justify-between">

                <div class="flex items-center gap-3">

                    <div
                        class="w-9 h-9 rounded-lg bg-blue-600
                               flex items-center justify-center
                               text-white font-bold"
                    >
                        S
                    </div>

                    <div>

                        <h1 class="font-bold text-slate-800">
                            SIMRS
                        </h1>

                        <p class="text-xs text-slate-500">
                            Hospital Management System
                        </p>

                    </div>

                </div>


                <button
                    @click="cancel"
                    class="text-sm text-slate-600
                           hover:text-blue-600"
                >
                    ← User Management
                </button>

            </div>

        </header>


        <!-- Content -->
        <main class="p-6">

            <div class="max-w-2xl mx-auto">

                <!-- Page Header -->
                <div class="mb-6">

                    <h2 class="text-2xl font-bold text-slate-800">
                        Tambah User
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Tambahkan pengguna baru ke sistem rumah sakit.
                    </p>

                </div>


                <!-- Form Card -->
                <div
                    class="bg-white rounded-2xl
                           border border-slate-200
                           p-6"
                >

                    <!-- Error -->
                    <div
                        v-if="error"
                        class="mb-5 p-4 rounded-xl
                               bg-red-50 border border-red-200
                               text-red-700 text-sm"
                    >
                        {{ error }}
                    </div>


                    <!-- Success -->
                    <div
                        v-if="success"
                        class="mb-5 p-4 rounded-xl
                               bg-green-50 border border-green-200
                               text-green-700 text-sm"
                    >
                        {{ success }}
                    </div>


                    <!-- Name -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Nama Lengkap
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Contoh: Dr. Budi Santoso"
                            class="w-full px-4 py-3
                                   rounded-xl border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Email -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Email
                        </label>

                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="dokter@simrs.test"
                            class="w-full px-4 py-3
                                   rounded-xl border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Password -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Password
                        </label>

                        <input
                            v-model="form.password"
                            type="password"
                            placeholder="Minimal 8 karakter"
                            class="w-full px-4 py-3
                                   rounded-xl border border-slate-300
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <!-- Role -->
                    <div class="mb-6">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Role
                        </label>

                        <select
                            v-model="form.role"
                            :disabled="loadingRoles"
                            class="w-full px-4 py-3
                                   rounded-xl border border-slate-300
                                   bg-white
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500"
                        >

                            <option value="">
                                Pilih Role
                            </option>

                            <option
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.name"
                            >
                                {{ role.name }}
                            </option>

                        </select>

                    </div>


                    <!-- Actions -->
                    <div class="flex justify-end gap-3">

                        <button
                            type="button"
                            @click="cancel"
                            class="px-5 py-3 rounded-xl
                                   border border-slate-300
                                   text-slate-700
                                   hover:bg-slate-50
                                   transition"
                        >
                            Batal
                        </button>


                        <button
                            type="button"
                            @click="submit"
                            :disabled="loading"
                            class="px-5 py-3 rounded-xl
                                   bg-blue-600 text-white
                                   font-semibold
                                   hover:bg-blue-700
                                   disabled:opacity-50
                                   transition"
                        >
                            {{ loading ? 'Menyimpan...' : 'Simpan User' }}
                        </button>

                    </div>

                </div>

            </div>

        </main>

    </div>

</template>
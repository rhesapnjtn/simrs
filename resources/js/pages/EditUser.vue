<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const user = ref({
    name: '',
    email: '',
    role: '',
});

const roles = ref([]);
const loading = ref(true);
const saving = ref(false);
const error = ref('');

const getData = async () => {
    try {
        const [userResponse, rolesResponse] = await Promise.all([
            axios.get(`/api/users/${route.params.id}`),
            axios.get('/api/roles'),
        ]);

        const data = userResponse.data.user;

        user.value = {
            name: data.name,
            email: data.email,
            role: data.roles?.[0]?.name || '',
        };

        roles.value = rolesResponse.data.roles;

    } catch (err) {
        console.error(err);

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil data user.';

    } finally {
        loading.value = false;
    }
};


const updateUser = async () => {

    error.value = '';

    if (!user.value.name || !user.value.email || !user.value.role) {
        error.value = 'Semua field wajib diisi.';
        return;
    }

    try {

        saving.value = true;

        await axios.put(
            `/api/users/${route.params.id}`,
            user.value
        );

        router.push('/users');

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
                'Gagal memperbarui user.';

        }

    } finally {

        saving.value = false;

    }
};


const cancel = () => {
    router.push('/users');
};


onMounted(() => {
    getData();
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

                <div class="mb-6">

                    <h2 class="text-2xl font-bold text-slate-800">
                        Edit User
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Perbarui informasi dan role pengguna.
                    </p>

                </div>


                <!-- Loading -->
                <div
                    v-if="loading"
                    class="bg-white rounded-2xl
                           border border-slate-200
                           p-8 text-center"
                >
                    Memuat data user...
                </div>


                <!-- Form -->
                <div
                    v-else
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


                    <!-- Name -->
                    <div class="mb-5">

                        <label
                            class="block text-sm font-medium
                                   text-slate-700 mb-2"
                        >
                            Nama Lengkap
                        </label>

                        <input
                            v-model="user.name"
                            type="text"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
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
                            v-model="user.email"
                            type="email"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
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
                            v-model="user.role"
                            class="w-full px-4 py-3 rounded-xl
                                   border border-slate-300
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
                            @click="cancel"
                            class="px-5 py-3 rounded-xl
                                   border border-slate-300
                                   text-slate-700
                                   hover:bg-slate-50"
                        >
                            Batal
                        </button>


                        <button
                            @click="updateUser"
                            :disabled="saving"
                            class="px-5 py-3 rounded-xl
                                   bg-blue-600 text-white
                                   font-semibold
                                   hover:bg-blue-700
                                   disabled:opacity-50"
                        >
                            {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>

                    </div>

                </div>

            </div>

        </main>

    </div>

</template>
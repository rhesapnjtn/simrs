<template>

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
                @click="router.push('/dashboard')"
                class="text-sm
                       text-slate-600
                       hover:text-blue-600"
            >
                ← Dashboard
            </button>

        </div>

    </header>


    <!-- ================================================= -->
    <!-- CONTENT -->
    <!-- ================================================= -->

    <main class="p-6">

        <!-- HEADER -->

        <div
            class="flex flex-col
                   md:flex-row
                   md:items-center
                   md:justify-between
                   gap-4 mb-6"
        >

            <div>

                <h2
                    class="text-2xl
                           font-bold
                           text-slate-800"
                >
                    Manajemen Obat
                </h2>

                <p
                    class="text-slate-500
                           mt-1"
                >
                    Kelola data obat dan stok obat rumah sakit.
                </p>

            </div>


            <button
                @click="openCreateModal"
                class="px-4 py-2
                       rounded-xl
                       bg-blue-600
                       text-white
                       text-sm
                       font-semibold
                       hover:bg-blue-700
                       transition"
            >
                + Tambah Obat
            </button>

        </div>


        <!-- ================================================= -->
        <!-- ALERT -->
        <!-- ================================================= -->

        <div
            v-if="error"
            class="mb-5 p-4
                   rounded-xl
                   bg-red-50
                   border border-red-200
                   text-red-700"
        >
            {{ error }}
        </div>


        <div
            v-if="success"
            class="mb-5 p-4
                   rounded-xl
                   bg-green-50
                   border border-green-200
                   text-green-700"
        >
            {{ success }}
        </div>


        <!-- ================================================= -->
        <!-- SEARCH -->
        <!-- ================================================= -->

        <div
            class="bg-white
                   rounded-2xl
                   border border-slate-200
                   p-5 mb-6"
        >

            <div
                class="flex flex-col
                       md:flex-row
                       gap-4"
            >

                <input
                    v-model="search"
                    @input="getObats"
                    type="text"
                    placeholder="Cari kode atau nama obat..."
                    class="flex-1
                           px-4 py-3
                           rounded-xl
                           border border-slate-300
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-500"
                />


                <select
                    v-model="filterActive"
                    @change="getObats"
                    class="md:w-48
                           px-4 py-3
                           rounded-xl
                           border border-slate-300
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-500"
                >

                    <option value="">
                        Semua Status
                    </option>

                    <option value="true">
                        Aktif
                    </option>

                    <option value="false">
                        Nonaktif
                    </option>

                </select>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- TABLE -->
        <!-- ================================================= -->

        <div
            class="bg-white
                   rounded-2xl
                   border border-slate-200
                   overflow-hidden"
        >

            <!-- LOADING -->

            <div
                v-if="loading"
                class="p-10
                       text-center
                       text-slate-500"
            >
                Memuat data obat...
            </div>


            <!-- EMPTY -->

            <div
                v-else-if="obats.length === 0"
                class="p-10
                       text-center
                       text-slate-500"
            >

                <div
                    class="text-4xl mb-3"
                >
                    💊
                </div>

                <p
                    class="font-medium
                           text-slate-700"
                >
                    Belum ada obat
                </p>

                <p
                    class="text-sm mt-1"
                >
                    Tambahkan obat terlebih dahulu.
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
                        class="bg-slate-50"
                    >

                        <tr>

                            <th
                                class="text-left
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Kode
                            </th>

                            <th
                                class="text-left
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Nama Obat
                            </th>

                            <th
                                class="text-left
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Jenis
                            </th>

                            <th
                                class="text-left
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Satuan
                            </th>

                            <th
                                class="text-right
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Stok
                            </th>

                            <th
                                class="text-right
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Harga
                            </th>

                            <th
                                class="text-center
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Status
                            </th>

                            <th
                                class="text-right
                                       px-5 py-4
                                       font-semibold
                                       text-slate-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="obat in obats"
                            :key="obat.id"
                            class="border-t
                                   border-slate-100
                                   hover:bg-slate-50"
                        >

                            <!-- KODE -->

                            <td
                                class="px-5 py-5
                                       font-semibold
                                       text-blue-600"
                            >
                                {{ obat.kode_obat }}
                            </td>


                            <!-- NAMA -->

                            <td
                                class="px-5 py-5"
                            >

                                <p
                                    class="font-semibold
                                           text-slate-800"
                                >
                                    {{ obat.nama_obat }}
                                </p>

                            </td>


                            <!-- JENIS -->

                            <td
                                class="px-5 py-5
                                       text-slate-600"
                            >
                                {{ obat.jenis || '-' }}
                            </td>


                            <!-- SATUAN -->

                            <td
                                class="px-5 py-5
                                       text-slate-600"
                            >
                                {{ obat.satuan }}
                            </td>


                            <!-- STOK -->

                            <td
                                class="px-5 py-5
                                       text-right"
                            >

                                <span
                                    :class="
                                        obat.stok <= 10
                                            ? 'text-red-600 font-bold'
                                            : 'text-slate-700 font-semibold'
                                    "
                                >
                                    {{ obat.stok }}
                                </span>

                            </td>


                            <!-- HARGA -->

                            <td
                                class="px-5 py-5
                                       text-right
                                       font-medium"
                            >
                                {{ formatRupiah(obat.harga) }}
                            </td>


                            <!-- STATUS -->

                            <td
                                class="px-5 py-5
                                       text-center"
                            >

                                <span
                                    class="px-3 py-1
                                           rounded-full
                                           text-xs
                                           font-semibold"
                                    :class="
                                        obat.is_active
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-slate-100 text-slate-500'
                                    "
                                >
                                    {{
                                        obat.is_active
                                            ? 'Aktif'
                                            : 'Nonaktif'
                                    }}
                                </span>

                            </td>


                            <!-- AKSI -->

                            <td
                                class="px-5 py-5"
                            >

                                <div
                                    class="flex
                                           justify-end
                                           gap-2"
                                >

                                    <button
                                        @click="openEditModal(obat)"
                                        class="px-3 py-2
                                               rounded-lg
                                               bg-blue-50
                                               text-blue-600
                                               text-xs
                                               font-semibold
                                               hover:bg-blue-100"
                                    >
                                        Edit
                                    </button>


                                    <button
                                        @click="deleteObat(obat)"
                                        class="px-3 py-2
                                               rounded-lg
                                               bg-red-50
                                               text-red-600
                                               text-xs
                                               font-semibold
                                               hover:bg-red-100"
                                    >
                                        Hapus
                                    </button>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </main>


    <!-- ================================================= -->
    <!-- MODAL -->
    <!-- ================================================= -->

    <div
        v-if="showModal"
        class="fixed inset-0
               z-50
               flex items-center
               justify-center
               bg-black/40
               p-4"
    >

        <div
            class="bg-white
                   w-full max-w-2xl
                   rounded-2xl
                   shadow-xl
                   overflow-hidden"
        >

            <!-- MODAL HEADER -->

            <div
                class="px-6 py-5
                       border-b
                       border-slate-200
                       flex items-center
                       justify-between"
            >

                <div>

                    <h3
                        class="text-lg
                               font-bold
                               text-slate-800"
                    >
                        {{
                            editing
                                ? 'Edit Obat'
                                : 'Tambah Obat'
                        }}
                    </h3>

                    <p
                        class="text-sm
                               text-slate-500
                               mt-1"
                    >
                        Lengkapi informasi obat.
                    </p>

                </div>


                <button
                    @click="closeModal"
                    class="text-slate-400
                           hover:text-slate-700
                           text-xl"
                >
                    ×
                </button>

            </div>


            <!-- FORM -->

            <form
                @submit.prevent="saveObat"
                class="p-6"
            >

                <div
                    class="grid grid-cols-1
                           md:grid-cols-2
                           gap-5"
                >

                    <!-- KODE -->

                    <div>

                        <label
                            class="block
                                   text-sm
                                   font-medium
                                   text-slate-700
                                   mb-2"
                        >
                            Kode Obat
                        </label>

                        <input
                            v-model="form.kode_obat"
                            type="text"
                            required
                            placeholder="OBT-001"
                            class="w-full
                                   px-4 py-3
                                   rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-500"
                        />

                    </div>


                    <!-- NAMA -->

                    <div>

                        <label
                            class="block
                                   text-sm
                                   font-medium
                                   text-slate-700
                                   mb-2"
                        >
                            Nama Obat
                        </label>

                        <input
                            v-model="form.nama_obat"
                            type="text"
                            required
                            placeholder="Paracetamol"
                            class="w-full
                                   px-4 py-3
                                   rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-500"
                        />

                    </div>


                    <!-- JENIS -->

                    <div>

                        <label
                            class="block
                                   text-sm
                                   font-medium
                                   text-slate-700
                                   mb-2"
                        >
                            Jenis
                        </label>

                        <input
                            v-model="form.jenis"
                            type="text"
                            placeholder="Tablet"
                            class="w-full
                                   px-4 py-3
                                   rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-500"
                        />

                    </div>


                    <!-- SATUAN -->

                    <div>

                        <label
                            class="block
                                   text-sm
                                   font-medium
                                   text-slate-700
                                   mb-2"
                        >
                            Satuan
                        </label>

                        <input
                            v-model="form.satuan"
                            type="text"
                            required
                            placeholder="Strip"
                            class="w-full
                                   px-4 py-3
                                   rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-500"
                        />

                    </div>


                    <!-- STOK -->

                    <div>

                        <label
                            class="block
                                   text-sm
                                   font-medium
                                   text-slate-700
                                   mb-2"
                        >
                            Stok
                        </label>

                        <input
                            v-model.number="form.stok"
                            type="number"
                            min="0"
                            required
                            class="w-full
                                   px-4 py-3
                                   rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-500"
                        />

                    </div>


                    <!-- HARGA -->

                    <div>

                        <label
                            class="block
                                   text-sm
                                   font-medium
                                   text-slate-700
                                   mb-2"
                        >
                            Harga
                        </label>

                        <input
                            v-model.number="form.harga"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="w-full
                                   px-4 py-3
                                   rounded-xl
                                   border border-slate-300
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-500"
                        />

                    </div>

                </div>


                <!-- STATUS -->

                <div
                    class="mt-5"
                >

                    <label
                        class="flex
                               items-center
                               gap-3"
                    >

                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="w-4 h-4
                                   rounded
                                   text-blue-600"
                        />

                        <span
                            class="text-sm
                                   font-medium
                                   text-slate-700"
                        >
                            Obat aktif
                        </span>

                    </label>

                </div>


                <!-- BUTTON -->

                <div
                    class="flex
                           justify-end
                           gap-3
                           mt-7"
                >

                    <button
                        type="button"
                        @click="closeModal"
                        class="px-4 py-2
                               rounded-xl
                               border border-slate-300
                               text-slate-700
                               text-sm
                               font-semibold
                               hover:bg-slate-50"
                    >
                        Batal
                    </button>


                    <button
                        type="submit"
                        :disabled="saving"
                        class="px-5 py-2
                               rounded-xl
                               bg-blue-600
                               text-white
                               text-sm
                               font-semibold
                               hover:bg-blue-700
                               disabled:opacity-50"
                    >
                        {{
                            saving
                                ? 'Menyimpan...'
                                : editing
                                    ? 'Simpan Perubahan'
                                    : 'Tambah Obat'
                        }}
                    </button>

                </div>

            </form>

        </div>

    </div>

</template>


<script setup>

import {
    ref,
    onMounted
} from 'vue';

import axios from 'axios';

import {
    useRouter
} from 'vue-router';


const router = useRouter();


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const obats = ref([]);

const loading = ref(false);

const saving = ref(false);

const error = ref('');

const success = ref('');

const search = ref('');

const filterActive = ref('');

const showModal = ref(false);

const editing = ref(false);

const editingId = ref(null);


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = ref({

    kode_obat: '',

    nama_obat: '',

    jenis: '',

    satuan: '',

    stok: 0,

    harga: 0,

    is_active: true,

});


/*
|--------------------------------------------------------------------------
| GET OBATS
|--------------------------------------------------------------------------
*/

const getObats = async () => {

    try {

        loading.value = true;

        error.value = '';

        const params = {};

        if (search.value) {

            params.search =
                search.value;

        }

        if (
            filterActive.value !== ''
        ) {

            params.is_active =
                filterActive.value;

        }

        const response =
            await axios.get(
                '/api/obats',
                {
                    params
                }
            );

        obats.value =
            response.data.obats || [];

    } catch (err) {

        console.error(
            'Gagal mengambil obat:',
            err
        );

        error.value =
            err.response?.data?.message ||
            'Gagal mengambil data obat.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| RESET FORM
|--------------------------------------------------------------------------
*/

const resetForm = () => {

    form.value = {

        kode_obat: '',

        nama_obat: '',

        jenis: '',

        satuan: '',

        stok: 0,

        harga: 0,

        is_active: true,

    };

    editing.value = false;

    editingId.value = null;

};


/*
|--------------------------------------------------------------------------
| OPEN CREATE
|--------------------------------------------------------------------------
*/

const openCreateModal = () => {

    resetForm();

    error.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| OPEN EDIT
|--------------------------------------------------------------------------
*/

const openEditModal = (obat) => {

    editing.value = true;

    editingId.value =
        obat.id;

    form.value = {

        kode_obat:
            obat.kode_obat,

        nama_obat:
            obat.nama_obat,

        jenis:
            obat.jenis || '',

        satuan:
            obat.satuan,

        stok:
            obat.stok,

        harga:
            obat.harga,

        is_active:
            Boolean(obat.is_active),

    };

    error.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| CLOSE MODAL
|--------------------------------------------------------------------------
*/

const closeModal = () => {

    showModal.value = false;

    resetForm();

};


/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

const saveObat = async () => {

    try {

        saving.value = true;

        error.value = '';

        success.value = '';

        if (editing.value) {

            await axios.put(
                `/api/obats/${editingId.value}`,
                form.value
            );

            success.value =
                'Obat berhasil diperbarui.';

        } else {

            await axios.post(
                '/api/obats',
                form.value
            );

            success.value =
                'Obat berhasil ditambahkan.';

        }

        closeModal();

        await getObats();

    } catch (err) {

        console.error(
            'Gagal menyimpan obat:',
            err
        );

        error.value =
            err.response?.data?.message ||
            'Gagal menyimpan data obat.';

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

const deleteObat = async (obat) => {

    const confirmed =
        confirm(
            `Hapus obat "${obat.nama_obat}"?`
        );

    if (!confirmed) {
        return;
    }

    try {

        error.value = '';

        success.value = '';

        await axios.delete(
            `/api/obats/${obat.id}`
        );

        success.value =
            'Obat berhasil dihapus.';

        await getObats();

    } catch (err) {

        console.error(
            'Gagal menghapus obat:',
            err
        );

        error.value =
            err.response?.data?.message ||
            'Gagal menghapus obat.';

    }

};


/*
|--------------------------------------------------------------------------
| FORMAT RUPIAH
|--------------------------------------------------------------------------
*/

const formatRupiah = (value) => {

    return new Intl.NumberFormat(
        'id-ID',
        {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }
    ).format(value || 0);

};


/*
|--------------------------------------------------------------------------
| ON MOUNTED
|--------------------------------------------------------------------------
*/

onMounted(() => {

    getObats();

});

</script>
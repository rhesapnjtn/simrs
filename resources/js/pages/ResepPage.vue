```vue
<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="mx-auto max-w-7xl">

            <!-- ================================================= -->
            <!-- HEADER -->
            <!-- ================================================= -->

            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Resep Obat
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Kelola resep dan obat pasien
                    </p>
                </div>

                <button
                    @click="openCreateModal"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    + Buat Resep
                </button>

            </div>


            <!-- ================================================= -->
            <!-- SUCCESS -->
            <!-- ================================================= -->

            <div
                v-if="successMessage"
                class="mb-5 flex items-center justify-between rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
            >
                <span>
                    {{ successMessage }}
                </span>

                <button
                    @click="successMessage = ''"
                    class="text-green-700 hover:text-green-900"
                >
                    ×
                </button>
            </div>


            <!-- ================================================= -->
            <!-- ERROR -->
            <!-- ================================================= -->

            <div
                v-if="errorMessage"
                class="mb-5 flex items-center justify-between rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                <span>
                    {{ errorMessage }}
                </span>

                <button
                    @click="errorMessage = ''"
                    class="text-red-700 hover:text-red-900"
                >
                    ×
                </button>
            </div>


            <!-- ================================================= -->
            <!-- TABLE -->
            <!-- ================================================= -->

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-200 px-6 py-4">

                    <h2 class="font-semibold text-gray-800">
                        Daftar Resep
                    </h2>

                    <p class="mt-1 text-xs text-gray-500">
                        Daftar resep yang telah dibuat.
                    </p>

                </div>


                <div class="overflow-x-auto">

                    <table class="w-full text-left text-sm">

                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">

                            <tr>

                                <th class="px-6 py-4">
                                    No. Resep
                                </th>

                                <th class="px-6 py-4">
                                    Pasien
                                </th>

                                <th class="px-6 py-4">
                                    Tanggal
                                </th>

                                <th class="px-6 py-4">
                                    Dokter
                                </th>

                                <th class="px-6 py-4">
                                    Jumlah Obat
                                </th>

                                <th class="px-6 py-4">
                                    Status
                                </th>

                                <th class="px-6 py-4 text-center">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            <!-- LOADING -->

                            <tr v-if="loading">

                                <td
                                    colspan="7"
                                    class="px-6 py-10 text-center text-gray-500"
                                >
                                    Memuat data resep...
                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr v-else-if="reseps.length === 0">

                                <td
                                    colspan="7"
                                    class="px-6 py-10 text-center"
                                >

                                    <div class="text-4xl">
                                        💊
                                    </div>

                                    <p class="mt-3 font-medium text-gray-700">
                                        Belum ada resep
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Belum terdapat resep yang dibuat.
                                    </p>

                                </td>

                            </tr>


                            <!-- DATA -->

                            <tr
                                v-for="resep in reseps"
                                :key="resep.id"
                                class="transition hover:bg-gray-50"
                            >

                                <!-- NO RESEP -->

                                <td class="px-6 py-4 font-semibold text-gray-800">

                                    {{ resep.no_resep }}

                                </td>


                                <!-- PASIEN -->

                                <td class="px-6 py-4">

                                    <div class="font-medium text-gray-800">
                                        {{ getPatientName(resep) }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ getPatientRM(resep) }}
                                    </div>

                                </td>


                                <!-- TANGGAL -->

                                <td class="px-6 py-4 text-gray-600">

                                    {{ formatDate(resep.tanggal_resep) }}

                                </td>


                                <!-- DOKTER -->

                                <td class="px-6 py-4 text-gray-600">

                                    {{ getDoctorName(resep) }}

                                </td>


                                <!-- JUMLAH OBAT -->

                                <td class="px-6 py-4 text-gray-600">

                                    {{ resep.details?.length ?? 0 }}
                                    obat

                                </td>


                                <!-- STATUS -->

                                <td class="px-6 py-4">

                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="statusClass(resep.status)"
                                    >
                                        {{ resep.status }}
                                    </span>

                                </td>


                                <!-- AKSI -->

                                <td class="px-6 py-4 text-center whitespace-nowrap">

                                    <button
                                        @click="viewResep(resep)"
                                        class="mr-2 rounded-lg bg-gray-100 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-200"
                                    >
                                        Detail
                                    </button>


                                    <button
                                        v-if="resep.status === 'MENUNGGU'"
                                        @click="processResep(resep)"
                                        class="mr-2 rounded-lg bg-blue-100 px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-200"
                                    >
                                        Proses
                                    </button>


                                    <button
                                        @click="deleteResep(resep)"
                                        class="rounded-lg bg-red-100 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-200"
                                    >
                                        Hapus
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- MODAL BUAT RESEP -->
        <!-- ================================================= -->

        <div
            v-if="showCreateModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >

            <div
                class="max-h-[92vh] w-full max-w-5xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
            >

                <!-- ================================================= -->
                <!-- MODAL HEADER -->
                <!-- ================================================= -->

                <div class="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-6 py-5">

                    <div>

                        <h2 class="text-xl font-bold text-gray-800">
                            Buat Resep Baru
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Cari pasien dan pilih kunjungan yang akan digunakan untuk resep.
                        </p>

                    </div>


                    <button
                        @click="closeCreateModal"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-2xl text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    >
                        ×
                    </button>

                </div>


                <!-- ================================================= -->
                <!-- FORM -->
                <!-- ================================================= -->

                <form
                    @submit.prevent="submitResep"
                    class="space-y-6 p-6"
                >

                    <!-- ================================================= -->
                    <!-- STEP 1 : CARI PASIEN -->
                    <!-- ================================================= -->

                    <div class="rounded-2xl border border-gray-200 bg-white">

                        <div class="border-b bg-gray-50 px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-600"
                                >
                                    1
                                </div>

                                <div>

                                    <h3 class="font-semibold text-gray-800">
                                        Pilih Pasien
                                    </h3>

                                    <p class="text-xs text-gray-500">
                                        Cari berdasarkan nama atau nomor rekam medis.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-5">

                            <!-- SEARCH -->

                            <div class="relative">

                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"
                                >
                                    🔍
                                </div>

                                <input
                                    v-model="patientSearch"
                                    type="text"
                                    placeholder="Cari nama pasien atau No. RM..."
                                    class="w-full rounded-xl border border-gray-300 py-3 pl-11 pr-4 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    autocomplete="off"
                                />

                            </div>


                            <!-- SEARCH LOADING -->

                            <div
                                v-if="patientSearchLoading"
                                class="mt-3 rounded-xl bg-gray-50 p-4 text-center text-sm text-gray-500"
                            >
                                Mencari pasien...
                            </div>


                            <!-- SEARCH RESULT -->

                            <div
                                v-if="
                                    patientSearch &&
                                    !patientSearchLoading &&
                                    patientResults.length > 0 &&
                                    !selectedPatient
                                "
                                class="mt-3 overflow-hidden rounded-xl border border-gray-200"
                            >

                                <button
                                    v-for="patient in patientResults"
                                    :key="patient.id"
                                    type="button"
                                    @click="selectPatient(patient)"
                                    class="flex w-full items-center gap-4 border-b border-gray-100 px-4 py-4 text-left transition last:border-b-0 hover:bg-blue-50"
                                >

                                    <div
                                        class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-600"
                                    >
                                        {{ getInitial(patient.nama || patient.nama_pasien) }}
                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <p class="font-semibold text-gray-800">
                                            {{ patient.nama || patient.nama_pasien || '-' }}
                                        </p>

                                        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">

                                            <span>
                                                RM: {{ patient.no_rm || '-' }}
                                            </span>

                                            <span v-if="patient.nik">
                                                NIK: {{ patient.nik }}
                                            </span>

                                        </div>

                                    </div>


                                    <span class="text-gray-400">
                                        →
                                    </span>

                                </button>

                            </div>


                            <!-- NO RESULT -->

                            <div
                                v-if="
                                    patientSearch.length >= 2 &&
                                    !patientSearchLoading &&
                                    patientResults.length === 0 &&
                                    !selectedPatient
                                "
                                class="mt-3 rounded-xl border border-gray-200 bg-gray-50 p-5 text-center"
                            >

                                <div class="text-3xl">
                                    🔍
                                </div>

                                <p class="mt-2 font-medium text-gray-700">
                                    Pasien tidak ditemukan
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    Coba gunakan nama atau nomor RM yang berbeda.
                                </p>

                            </div>


                            <!-- SELECTED PATIENT -->

                            <div
                                v-if="selectedPatient"
                                class="mt-4 rounded-xl border border-blue-200 bg-blue-50 p-4"
                            >

                                <div class="flex items-center justify-between gap-4">

                                    <div class="flex items-center gap-4">

                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 font-bold text-white"
                                        >
                                            {{ getInitial(getPatientNameFromData(selectedPatient)) }}
                                        </div>


                                        <div>

                                            <p class="font-bold text-gray-800">
                                                {{ getPatientNameFromData(selectedPatient) }}
                                            </p>

                                            <div class="mt-1 flex flex-wrap gap-x-4 text-xs text-gray-600">

                                                <span>
                                                    RM: {{ selectedPatient.no_rm || '-' }}
                                                </span>

                                                <span v-if="selectedPatient.jenis_kelamin">
                                                    {{ selectedPatient.jenis_kelamin }}
                                                </span>

                                            </div>

                                        </div>

                                    </div>


                                    <button
                                        type="button"
                                        @click="clearSelectedPatient"
                                        class="rounded-lg bg-white px-3 py-2 text-xs font-medium text-red-600 shadow-sm hover:bg-red-50"
                                    >
                                        Ganti Pasien
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- STEP 2 : KUNJUNGAN -->
                    <!-- ================================================= -->

                    <div
                        v-if="selectedPatient"
                        class="rounded-2xl border border-gray-200 bg-white"
                    >

                        <div class="border-b bg-gray-50 px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-600"
                                >
                                    2
                                </div>

                                <div>

                                    <h3 class="font-semibold text-gray-800">
                                        Pilih Kunjungan
                                    </h3>

                                    <p class="text-xs text-gray-500">
                                        Pilih kunjungan pasien untuk resep ini.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-5">

                            <!-- LOADING -->

                            <div
                                v-if="loadingVisits"
                                class="rounded-xl bg-gray-50 p-8 text-center text-sm text-gray-500"
                            >
                                Memuat riwayat kunjungan pasien...
                            </div>


                            <!-- EMPTY -->

                            <div
                                v-else-if="patientVisits.length === 0"
                                class="rounded-xl border border-yellow-200 bg-yellow-50 p-5"
                            >

                                <div class="flex gap-3">

                                    <div class="text-2xl">
                                        ⚠️
                                    </div>

                                    <div>

                                        <p class="font-semibold text-yellow-800">
                                            Belum ada kunjungan
                                        </p>

                                        <p class="mt-1 text-sm text-yellow-700">
                                            Pasien ini belum memiliki data pendaftaran yang dapat digunakan untuk resep.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- VISITS -->

                            <div
                                v-else
                                class="space-y-3"
                            >

                                <button
                                    v-for="visit in patientVisits"
                                    :key="visit.id"
                                    type="button"
                                    @click="selectVisit(visit)"
                                    class="w-full rounded-xl border p-4 text-left transition"
                                    :class="
                                        selectedVisit?.id === visit.id
                                            ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50'
                                    "
                                >

                                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                                        <div class="flex items-start gap-4">

                                            <div
                                                class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-gray-100 text-xl"
                                            >
                                                🏥
                                            </div>


                                            <div>

                                                <div class="flex flex-wrap items-center gap-2">

                                                    <p class="font-bold text-gray-800">
                                                        {{ formatDate(visit.tanggal_kunjungan) }}
                                                    </p>

                                                    <span
                                                        v-if="visit.no_antrian"
                                                        class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600"
                                                    >
                                                        {{ visit.no_antrian }}
                                                    </span>

                                                </div>


                                                <p class="mt-1 text-sm text-gray-600">
                                                    {{ getPoliName(visit) }}
                                                </p>


                                                <p class="mt-1 text-sm text-gray-500">
                                                    Dr. {{ getDoctorNameFromVisit(visit) }}
                                                </p>

                                            </div>

                                        </div>


                                        <div class="flex items-center gap-3">

                                            <span
                                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                                :class="visitStatusClass(visit.status)"
                                            >
                                                {{ visit.status || 'TERDAFTAR' }}
                                            </span>


                                            <span
                                                v-if="selectedVisit?.id === visit.id"
                                                class="font-bold text-blue-600"
                                            >
                                                ✓
                                            </span>

                                        </div>

                                    </div>

                                </button>

                            </div>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- DATA KUNJUNGAN TERPILIH -->
                    <!-- ================================================= -->

                    <div
                        v-if="selectedVisit"
                        class="rounded-2xl border border-green-200 bg-green-50 p-5"
                    >

                        <div class="mb-4 flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-green-600 font-bold text-white"
                            >
                                ✓
                            </div>

                            <div>

                                <h3 class="font-semibold text-green-800">
                                    Kunjungan Terpilih
                                </h3>

                                <p class="text-xs text-green-700">
                                    Data resep akan dikaitkan dengan kunjungan ini.
                                </p>

                            </div>

                        </div>


                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

                            <div>
                                <p class="text-xs text-green-700">
                                    Pasien
                                </p>

                                <p class="mt-1 font-semibold text-green-900">
                                    {{ getPatientNameFromData(selectedPatient) }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-green-700">
                                    No. RM
                                </p>

                                <p class="mt-1 font-semibold text-green-900">
                                    {{ selectedPatient.no_rm || '-' }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-green-700">
                                    Poli
                                </p>

                                <p class="mt-1 font-semibold text-green-900">
                                    {{ getPoliName(selectedVisit) }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-green-700">
                                    Dokter
                                </p>

                                <p class="mt-1 font-semibold text-green-900">
                                    Dr. {{ getDoctorNameFromVisit(selectedVisit) }}
                                </p>
                            </div>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- STEP 3 : TANGGAL -->
                    <!-- ================================================= -->

                    <div
                        v-if="selectedVisit"
                        class="rounded-2xl border border-gray-200 bg-white p-5"
                    >

                        <h3 class="font-semibold text-gray-800">
                            Tanggal Resep
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            Tanggal pembuatan resep.
                        </p>


                        <input
                            v-model="form.tanggal_resep"
                            type="date"
                            required
                            class="mt-4 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 md:w-1/2"
                        />

                    </div>


                    <!-- ================================================= -->
                    <!-- STEP 4 : OBAT -->
                    <!-- ================================================= -->

                    <div
                        v-if="selectedVisit"
                        class="rounded-2xl border border-gray-200 bg-white"
                    >

                        <div class="flex flex-col gap-3 border-b bg-gray-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                            <div>

                                <h3 class="font-semibold text-gray-800">
                                    Obat
                                </h3>

                                <p class="text-xs text-gray-500">
                                    Tambahkan obat yang diresepkan.
                                </p>

                            </div>


                            <button
                                type="button"
                                @click="addDetail"
                                class="rounded-lg bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-700"
                            >
                                + Tambah Obat
                            </button>

                        </div>


                        <div class="space-y-5 p-5">

                            <div
                                v-for="(detail, index) in form.details"
                                :key="index"
                                class="rounded-xl border border-gray-200 bg-white p-5"
                            >

                                <div class="mb-4 flex items-center justify-between">

                                    <div>

                                        <h4 class="font-semibold text-gray-700">
                                            Obat {{ index + 1 }}
                                        </h4>

                                    </div>


                                    <button
                                        v-if="form.details.length > 1"
                                        type="button"
                                        @click="removeDetail(index)"
                                        class="text-sm font-medium text-red-600 hover:text-red-700"
                                    >
                                        Hapus
                                    </button>

                                </div>


                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                    <!-- OBAT -->

                                    <div class="md:col-span-2">

                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                            Nama Obat
                                        </label>

                                        <select
                                            v-model="detail.obat_id"
                                            required
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        >

                                            <option value="">
                                                Pilih obat
                                            </option>

                                            <option
                                                v-for="obat in activeObats"
                                                :key="obat.id"
                                                :value="obat.id"
                                            >
                                                {{ obat.kode_obat }}
                                                -
                                                {{ obat.nama_obat }}
                                                (Stok: {{ obat.stok }})
                                            </option>

                                        </select>

                                    </div>


                                    <!-- JUMLAH -->

                                    <div>

                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                            Jumlah
                                        </label>

                                        <input
                                            v-model.number="detail.jumlah"
                                            type="number"
                                            min="1"
                                            required
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        />

                                    </div>


                                    <!-- DOSIS -->

                                    <div>

                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                            Dosis
                                        </label>

                                        <input
                                            v-model="detail.dosis"
                                            type="text"
                                            placeholder="Contoh: 500 mg"
                                            required
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        />

                                    </div>


                                    <!-- ATURAN -->

                                    <div>

                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                            Aturan Pakai
                                        </label>

                                        <input
                                            v-model="detail.aturan_pakai"
                                            type="text"
                                            placeholder="Contoh: 3 x sehari"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        />

                                    </div>


                                    <!-- CATATAN -->

                                    <div>

                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                            Catatan
                                        </label>

                                        <input
                                            v-model="detail.catatan"
                                            type="text"
                                            placeholder="Opsional"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        />

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- CATATAN RESEP -->
                    <!-- ================================================= -->

                    <div
                        v-if="selectedVisit"
                        class="rounded-2xl border border-gray-200 bg-white p-5"
                    >

                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Catatan Resep
                        </label>

                        <textarea
                            v-model="form.catatan"
                            rows="3"
                            placeholder="Catatan tambahan untuk resep..."
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        ></textarea>

                    </div>


                    <!-- ================================================= -->
                    <!-- ACTION -->
                    <!-- ================================================= -->

                    <div class="sticky bottom-0 flex flex-col-reverse gap-3 border-t bg-white pt-5 sm:flex-row sm:justify-end">

                        <button
                            type="button"
                            @click="closeCreateModal"
                            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>


                        <button
                            v-if="selectedVisit"
                            type="submit"
                            :disabled="submitting"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ submitting ? 'Menyimpan...' : 'Simpan Resep' }}
                        </button>

                    </div>

                </form>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- MODAL DETAIL RESEP -->
        <!-- ================================================= -->

        <div
            v-if="selectedResep"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >

            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-xl">

                <!-- HEADER -->

                <div class="flex items-center justify-between border-b px-6 py-5">

                    <div>

                        <h2 class="text-xl font-bold text-gray-800">
                            Detail Resep
                        </h2>

                        <p class="text-sm text-gray-500">
                            {{ selectedResep.no_resep }}
                        </p>

                    </div>


                    <button
                        @click="selectedResep = null"
                        class="text-2xl text-gray-400 hover:text-gray-700"
                    >
                        ×
                    </button>

                </div>


                <div class="space-y-6 p-6">

                    <!-- INFO -->

                    <div class="grid grid-cols-1 gap-4 rounded-xl bg-gray-50 p-5 md:grid-cols-4">

                        <div>

                            <p class="text-xs text-gray-500">
                                No. Resep
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ selectedResep.no_resep }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Pasien
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ getPatientName(selectedResep) }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Dokter
                            </p>

                            <p class="mt-1 font-semibold text-gray-800">
                                {{ getDoctorName(selectedResep) }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Status
                            </p>

                            <span
                                class="mt-1 inline-block rounded-full px-3 py-1 text-xs font-semibold"
                                :class="statusClass(selectedResep.status)"
                            >
                                {{ selectedResep.status }}
                            </span>

                        </div>

                    </div>


                    <!-- DETAIL OBAT -->

                    <div>

                        <h3 class="mb-3 font-semibold text-gray-800">
                            Daftar Obat
                        </h3>


                        <div class="overflow-hidden rounded-xl border">

                            <div class="overflow-x-auto">

                                <table class="w-full text-left text-sm">

                                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">

                                        <tr>

                                            <th class="px-4 py-3">
                                                Obat
                                            </th>

                                            <th class="px-4 py-3">
                                                Jumlah
                                            </th>

                                            <th class="px-4 py-3">
                                                Dosis
                                            </th>

                                            <th class="px-4 py-3">
                                                Aturan Pakai
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody class="divide-y">

                                        <tr
                                            v-for="detail in selectedResep.details"
                                            :key="detail.id"
                                        >

                                            <td class="px-4 py-4 font-medium text-gray-800">
                                                {{ detail.obat?.nama_obat ?? '-' }}
                                            </td>

                                            <td class="px-4 py-4 text-gray-600">
                                                {{ detail.jumlah }}
                                            </td>

                                            <td class="px-4 py-4 text-gray-600">
                                                {{ detail.dosis }}
                                            </td>

                                            <td class="px-4 py-4 text-gray-600">
                                                {{ detail.aturan_pakai ?? '-' }}
                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>


                    <!-- CATATAN -->

                    <div
                        v-if="selectedResep.catatan"
                        class="rounded-xl bg-yellow-50 p-4"
                    >

                        <p class="text-xs font-semibold uppercase text-yellow-700">
                            Catatan
                        </p>

                        <p class="mt-1 text-sm text-yellow-800">
                            {{ selectedResep.catatan }}
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>
</template>


<script setup>

import {
    ref,
    computed,
    onMounted,
    watch
} from 'vue'

import axios from 'axios'


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const reseps = ref([])

const obats = ref([])

const loading = ref(false)

const submitting = ref(false)

const successMessage = ref('')

const errorMessage = ref('')

const showCreateModal = ref(false)

const selectedResep = ref(null)


/*
|--------------------------------------------------------------------------
| PATIENT SEARCH
|--------------------------------------------------------------------------
*/

const patientSearch = ref('')

const patientResults = ref([])

const patientSearchLoading = ref(false)

const selectedPatient = ref(null)


/*
|--------------------------------------------------------------------------
| VISITS
|--------------------------------------------------------------------------
*/

const patientVisits = ref([])

const loadingVisits = ref(false)

const selectedVisit = ref(null)


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = ref({
    pendaftaran_id: '',
    dokter_id: '',
    tanggal_resep: getToday(),
    catatan: '',
    details: [
        createEmptyDetail()
    ]
})


/*
|--------------------------------------------------------------------------
| COMPUTED
|--------------------------------------------------------------------------
*/

const activeObats = computed(() => {

    return obats.value.filter(
        obat => obat.is_active
    )

})


/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function getToday() {

    const date = new Date()

    const year = date.getFullYear()

    const month =
        String(date.getMonth() + 1)
            .padStart(2, '0')

    const day =
        String(date.getDate())
            .padStart(2, '0')

    return `${year}-${month}-${day}`

}


function createEmptyDetail() {

    return {
        obat_id: '',
        jumlah: 1,
        dosis: '',
        aturan_pakai: '',
        catatan: ''
    }

}


function getInitial(name) {

    if (!name) {
        return 'P'
    }

    return name
        .charAt(0)
        .toUpperCase()

}


function getPatientNameFromData(patient) {

    return (
        patient?.nama ??
        patient?.nama_pasien ??
        patient?.name ??
        '-'
    )

}


function getPatientName(resep) {

    return (
        resep?.pendaftaran?.pasien?.nama ??
        resep?.pendaftaran?.pasien?.nama_pasien ??
        resep?.pendaftaran?.nama_pasien ??
        '-'
    )

}


function getPatientRM(resep) {

    return (
        resep?.pendaftaran?.pasien?.no_rm ??
        '-'
    )

}


function getDoctorName(resep) {

    if (!resep?.dokter) {
        return '-'
    }

    return (
        resep.dokter.nama_dokter ??
        resep.dokter.nama ??
        resep.dokter.name ??
        '-'
    )

}


function getDoctorNameFromVisit(visit) {

    return (
        visit?.dokter?.nama_dokter ??
        visit?.dokter?.nama ??
        visit?.dokter?.name ??
        '-'
    )

}


function getPoliName(visit) {

    return (
        visit?.poli?.nama ??
        visit?.poli?.nama_poli ??
        '-'
    )

}


function formatDate(date) {

    if (!date) {
        return '-'
    }

    return new Date(date).toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }
    )

}


function statusClass(status) {

    switch (status) {

        case 'MENUNGGU':
            return 'bg-yellow-100 text-yellow-700'

        case 'DIPROSES':
            return 'bg-blue-100 text-blue-700'

        case 'SELESAI':
            return 'bg-green-100 text-green-700'

        case 'BATAL':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-gray-100 text-gray-700'

    }

}


function visitStatusClass(status) {

    switch (status) {

        case 'MENUNGGU':
            return 'bg-yellow-100 text-yellow-700'

        case 'DIPERIKSA':
            return 'bg-blue-100 text-blue-700'

        case 'SELESAI':
            return 'bg-green-100 text-green-700'

        case 'BATAL':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-gray-100 text-gray-700'

    }

}


/*
|--------------------------------------------------------------------------
| RESET FORM
|--------------------------------------------------------------------------
*/

function resetForm() {

    form.value = {

        pendaftaran_id: '',

        dokter_id: '',

        tanggal_resep: getToday(),

        catatan: '',

        details: [
            createEmptyDetail()
        ]

    }

    patientSearch.value = ''

    patientResults.value = []

    selectedPatient.value = null

    patientVisits.value = []

    selectedVisit.value = null

    errorMessage.value = ''

}


/*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/

function openCreateModal() {

    resetForm()

    showCreateModal.value = true

    loadObats()

}


function closeCreateModal() {

    showCreateModal.value = false

}


/*
|--------------------------------------------------------------------------
| SEARCH PASIEN
|--------------------------------------------------------------------------
*/

let searchTimer = null


watch(
    patientSearch,
    (value) => {

        clearTimeout(searchTimer)

        patientResults.value = []

        if (!value || value.trim().length < 2) {
            return
        }

        searchTimer = setTimeout(
            () => {
                searchPatients(value.trim())
            },
            350
        )

    }
)


async function searchPatients(keyword) {

    patientSearchLoading.value = true

    try {

        /*
        |--------------------------------------------------------------------------
        | Endpoint pasien
        |--------------------------------------------------------------------------
        |
        | Endpoint ini menggunakan API pasien yang sudah ada.
        |
        */

        const response = await axios.get(
            '/api/pasiens',
            {
                params: {
                    search: keyword
                },
                withCredentials: true
            }
        )


        /*
        |--------------------------------------------------------------------------
        | Kompatibel dengan beberapa bentuk response
        |--------------------------------------------------------------------------
        */

        patientResults.value =
            response.data.pasiens ??
            response.data.data ??
            []

    } catch (error) {

        console.error(
            'Gagal mencari pasien:',
            error
        )

        patientResults.value = []

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal mencari pasien.'

    } finally {

        patientSearchLoading.value = false

    }

}


/*
|--------------------------------------------------------------------------
| PILIH PASIEN
|--------------------------------------------------------------------------
*/

async function selectPatient(patient) {

    selectedPatient.value = patient

    patientSearch.value =
        getPatientNameFromData(patient)

    patientResults.value = []

    selectedVisit.value = null

    patientVisits.value = []

    form.value.pendaftaran_id = ''

    form.value.dokter_id = ''

    await loadPatientVisits(
        patient.id
    )

}


/*
|--------------------------------------------------------------------------
| GANTI PASIEN
|--------------------------------------------------------------------------
*/

function clearSelectedPatient() {

    selectedPatient.value = null

    selectedVisit.value = null

    patientVisits.value = []

    patientSearch.value = ''

    patientResults.value = []

    form.value.pendaftaran_id = ''

    form.value.dokter_id = ''

}


/*
|--------------------------------------------------------------------------
| LOAD KUNJUNGAN PASIEN
|--------------------------------------------------------------------------
*/

async function loadPatientVisits(pasienId) {

    loadingVisits.value = true

    try {

        /*
        |--------------------------------------------------------------------------
        | Ambil seluruh pendaftaran
        |--------------------------------------------------------------------------
        |
        | Untuk sementara kita gunakan endpoint yang sudah kamu punya.
        |
        */

        const response = await axios.get(
            '/api/pendaftarans',
            {
                withCredentials: true
            }
        )


        const allPendaftarans =
            response.data.pendaftarans ??
            response.data.data ??
            []


        /*
        |--------------------------------------------------------------------------
        | Filter berdasarkan pasien
        |--------------------------------------------------------------------------
        */

        patientVisits.value =
            allPendaftarans
                .filter(
                    pendaftaran => {

                        const currentPasienId =
                            pendaftaran.pasien_id ??
                            pendaftaran.pasien?.id

                        return String(currentPasienId) ===
                            String(pasienId)

                    }
                )
                .sort(
                    (a, b) => {

                        return new Date(
                            b.tanggal_kunjungan
                        ) -
                        new Date(
                            a.tanggal_kunjungan
                        )

                    }
                )

    } catch (error) {

        console.error(
            'Gagal mengambil kunjungan pasien:',
            error
        )

        patientVisits.value = []

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal mengambil kunjungan pasien.'

    } finally {

        loadingVisits.value = false

    }

}


/*
|--------------------------------------------------------------------------
| PILIH KUNJUNGAN
|--------------------------------------------------------------------------
*/

function selectVisit(visit) {

    selectedVisit.value = visit

    form.value.pendaftaran_id =
        visit.id

    form.value.dokter_id =
        visit.dokter_id ??
        visit.dokter?.id ??
        ''

}


/*
|--------------------------------------------------------------------------
| TAMBAH / HAPUS OBAT
|--------------------------------------------------------------------------
*/

function addDetail() {

    form.value.details.push(
        createEmptyDetail()
    )

}


function removeDetail(index) {

    if (
        form.value.details.length <= 1
    ) {
        return
    }

    form.value.details.splice(
        index,
        1
    )

}


/*
|--------------------------------------------------------------------------
| LOAD RESEP
|--------------------------------------------------------------------------
*/

async function loadReseps() {

    loading.value = true

    try {

        const response =
            await axios.get(
                '/api/reseps',
                {
                    withCredentials: true
                }
            )

        reseps.value =
            response.data.data ??
            []

    } catch (error) {

        console.error(error)

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal mengambil data resep.'

    } finally {

        loading.value = false

    }

}


/*
|--------------------------------------------------------------------------
| LOAD OBAT
|--------------------------------------------------------------------------
*/

async function loadObats() {

    try {

        const response =
            await axios.get(
                '/api/obats',
                {
                    withCredentials: true
                }
            )

        obats.value =
            response.data.obats ??
            response.data.data ??
            []

    } catch (error) {

        console.error(error)

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal mengambil data obat.'

    }

}


/*
|--------------------------------------------------------------------------
| SUBMIT RESEP
|--------------------------------------------------------------------------
*/

async function submitResep() {

    errorMessage.value = ''

    successMessage.value = ''


    if (!selectedPatient.value) {

        errorMessage.value =
            'Silakan pilih pasien terlebih dahulu.'

        return

    }


    if (!selectedVisit.value) {

        errorMessage.value =
            'Silakan pilih kunjungan pasien terlebih dahulu.'

        return

    }


    if (!form.value.pendaftaran_id) {

        errorMessage.value =
            'Pendaftaran pasien tidak ditemukan.'

        return

    }


    if (!form.value.dokter_id) {

        errorMessage.value =
            'Dokter dari kunjungan tidak ditemukan.'

        return

    }


    if (
        !form.value.details.length
    ) {

        errorMessage.value =
            'Minimal harus ada satu obat.'

        return

    }


    submitting.value = true


    try {

        const response =
            await axios.post(
                '/api/reseps',
                {

                    pendaftaran_id:
                        form.value.pendaftaran_id,

                    dokter_id:
                        form.value.dokter_id,

                    tanggal_resep:
                        form.value.tanggal_resep,

                    catatan:
                        form.value.catatan,

                    details:
                        form.value.details

                },
                {
                    withCredentials: true
                }
            )


        successMessage.value =
            response.data.message ??
            'Resep berhasil dibuat.'


        closeCreateModal()

        await loadReseps()

    } catch (error) {

        console.error(
            'Gagal membuat resep:',
            error
        )


        if (
            error.response?.data?.errors
        ) {

            const errors =
                error.response.data.errors

            const firstError =
                Object.values(errors)[0]

            errorMessage.value =
                Array.isArray(firstError)
                    ? firstError[0]
                    : 'Data resep tidak valid.'

        } else {

            errorMessage.value =
                error.response?.data?.message ??
                'Gagal membuat resep.'

        }

    } finally {

        submitting.value = false

    }

}


/*
|--------------------------------------------------------------------------
| DETAIL RESEP
|--------------------------------------------------------------------------
*/

async function viewResep(resep) {

    try {

        const response =
            await axios.get(
                `/api/reseps/${resep.id}`,
                {
                    withCredentials: true
                }
            )

        selectedResep.value =
            response.data.data

    } catch (error) {

        console.error(error)

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal mengambil detail resep.'

    }

}


/*
|--------------------------------------------------------------------------
| PROSES RESEP
|--------------------------------------------------------------------------
*/

async function processResep(resep) {

    if (
        !confirm(
            `Proses resep ${resep.no_resep}?`
        )
    ) {
        return
    }


    try {

        await axios.put(
            `/api/reseps/${resep.id}/status`,
            {
                status: 'DIPROSES'
            },
            {
                withCredentials: true
            }
        )


        successMessage.value =
            'Resep berhasil diproses.'


        await loadReseps()

    } catch (error) {

        console.error(error)

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal memproses resep.'

    }

}


/*
|--------------------------------------------------------------------------
| DELETE RESEP
|--------------------------------------------------------------------------
*/

async function deleteResep(resep) {

    if (
        !confirm(
            `Apakah Anda yakin ingin menghapus resep ${resep.no_resep}?`
        )
    ) {
        return
    }


    try {

        await axios.delete(
            `/api/reseps/${resep.id}`,
            {
                withCredentials: true
            }
        )


        successMessage.value =
            'Resep berhasil dihapus.'


        await loadReseps()

    } catch (error) {

        console.error(error)

        errorMessage.value =
            error.response?.data?.message ??
            'Gagal menghapus resep.'

    }

}


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadReseps()

})
</script>
```

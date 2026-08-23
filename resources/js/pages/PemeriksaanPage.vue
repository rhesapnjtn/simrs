<script setup>

import {
    ref,
    computed,
    onMounted,
    watch
} from 'vue';

import {
    useRoute,
    useRouter
} from 'vue-router';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/

const route = useRoute();
const router = useRouter();


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const loading = ref(false);
const saving = ref(false);

const error = ref('');
const success = ref('');

const pendaftarans = ref([]);

const pendaftaran = ref(null);
const pemeriksaan = ref(null);


/*
|--------------------------------------------------------------------------
| HASIL LAB
|--------------------------------------------------------------------------
*/

const hasilLabs = ref([]);
const loadingHasilLab = ref(false);
const hasilLabError = ref('');


/*
|--------------------------------------------------------------------------
| LAB STATE
|--------------------------------------------------------------------------
*/

const showLabModal = ref(false);

const loadingLab = ref(false);
const savingLab = ref(false);

const labPemeriksaans = ref([]);
const selectedLabIds = ref([]);

const labCatatan = ref('');

const labError = ref('');
const labSuccess = ref('');


/*
|--------------------------------------------------------------------------
| NOTIFICATION
|--------------------------------------------------------------------------
*/

const notification = ref({

    show: false,

    type: 'success',

    title: '',

    message: '',

});


let notificationTimer = null;


/*
|--------------------------------------------------------------------------
| FORM PEMERIKSAAN
|--------------------------------------------------------------------------
*/

const form = ref({

    tekanan_darah: '',

    suhu: '',

    berat_badan: '',

    tinggi_badan: '',

    nadi: '',

    respirasi: '',

    diagnosis: '',

    tindakan: '',

    catatan: '',

});


/*
|--------------------------------------------------------------------------
| MODE DETAIL
|--------------------------------------------------------------------------
*/

const isDetail = computed(() => {

    return !!route.params.id;

});


/*
|--------------------------------------------------------------------------
| ID PENDAFTARAN
|--------------------------------------------------------------------------
*/

const pendaftaranId = computed(() => {

    return route.params.id;

});


/*
|--------------------------------------------------------------------------
| TANGGAL HARI INI
|--------------------------------------------------------------------------
*/

const getToday = () => {

    const now = new Date();

    return (

        now.getFullYear() +

        '-' +

        String(
            now.getMonth() + 1
        ).padStart(2, '0') +

        '-' +

        String(
            now.getDate()
        ).padStart(2, '0')

    );

};


/*
|--------------------------------------------------------------------------
| NOTIFICATION
|--------------------------------------------------------------------------
*/

const showNotification = (
    type,
    title,
    message,
    duration = 4000
) => {

    if (notificationTimer) {

        clearTimeout(notificationTimer);

    }


    notification.value = {

        show: true,

        type,

        title,

        message,

    };


    notificationTimer = setTimeout(() => {

        notification.value.show = false;

    }, duration);

};


const closeNotification = () => {

    notification.value.show = false;


    if (notificationTimer) {

        clearTimeout(notificationTimer);

        notificationTimer = null;

    }

};


/*
|--------------------------------------------------------------------------
| RESET FORM
|--------------------------------------------------------------------------
*/

const resetForm = () => {

    form.value = {

        tekanan_darah: '',

        suhu: '',

        berat_badan: '',

        tinggi_badan: '',

        nadi: '',

        respirasi: '',

        diagnosis: '',

        tindakan: '',

        catatan: '',

    };

};


/*
|--------------------------------------------------------------------------
| GET PENDAFTARANS
|--------------------------------------------------------------------------
*/

const getPendaftarans = async () => {

    try {

        loading.value = true;

        error.value = '';

        success.value = '';


        const response = await axios.get(
            '/api/pendaftarans',
            {
                params: {
                    tanggal: getToday()
                }
            }
        );


        console.log(
            'DATA PENDAFTARAN:',
            response.data
        );


        if (Array.isArray(response.data)) {

            pendaftarans.value =
                response.data;

        } else {

            pendaftarans.value =

                response.data?.pendaftarans ||

                response.data?.data ||

                [];

        }


    } catch (err) {

        console.error(
            'Gagal mengambil daftar pemeriksaan:',
            err
        );


        error.value =

            err.response?.data?.message ||

            'Gagal mengambil data pasien.';


    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| PASIEN SIAP DIPERIKSA
|--------------------------------------------------------------------------
*/

const pasienUntukDiperiksa = computed(() => {

    return pendaftarans.value.filter(item => {

        return (

            item.status === 'DIPANGGIL' ||

            item.status === 'DIPERIKSA'

        );

    });

});


/*
|--------------------------------------------------------------------------
| GET MASTER PEMERIKSAAN LAB
|--------------------------------------------------------------------------
*/

const getLabPemeriksaans = async () => {

    try {

        loadingLab.value = true;

        labError.value = '';


        const response = await axios.get(
            '/api/lab-pemeriksaans/active'
        );


        console.log(
            'MASTER LAB:',
            response.data
        );


        if (Array.isArray(response.data)) {

            labPemeriksaans.value =
                response.data;

        } else {

            labPemeriksaans.value =

                response.data?.data ||

                response.data?.labPemeriksaans ||

                [];

        }


    } catch (err) {

        console.error(
            'Gagal mengambil pemeriksaan lab:',
            err
        );


        labError.value =

            err.response?.data?.message ||

            'Gagal mengambil daftar pemeriksaan laboratorium.';


    } finally {

        loadingLab.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| BUKA MODAL LAB
|--------------------------------------------------------------------------
*/

const bukaModalLab = async () => {

    labError.value = '';

    labSuccess.value = '';

    selectedLabIds.value = [];

    labCatatan.value = '';

    showLabModal.value = true;

    await getLabPemeriksaans();

};


/*
|--------------------------------------------------------------------------
| TUTUP MODAL LAB
|--------------------------------------------------------------------------
*/

const tutupModalLab = () => {

    if (savingLab.value) {

        return;

    }


    showLabModal.value = false;

    selectedLabIds.value = [];

    labCatatan.value = '';

    labError.value = '';

    labSuccess.value = '';

};


/*
|--------------------------------------------------------------------------
| SIMPAN PERMINTAAN LAB
|--------------------------------------------------------------------------
*/

const simpanPermintaanLab = async () => {

    if (!pendaftaran.value) {

        labError.value =
            'Data pendaftaran pasien tidak ditemukan.';

        showNotification(
            'error',
            'Permintaan Lab Gagal',
            'Data pendaftaran pasien tidak ditemukan.'
        );

        return;

    }


    if (selectedLabIds.value.length === 0) {

        labError.value =
            'Pilih minimal satu pemeriksaan laboratorium.';

        showNotification(
            'warning',
            'Pemeriksaan Belum Dipilih',
            'Silakan pilih minimal satu pemeriksaan laboratorium.'
        );

        return;

    }


    try {

        savingLab.value = true;

        labError.value = '';

        labSuccess.value = '';


        const dokterId =

            pendaftaran.value.dokter_id ||

            pendaftaran.value.dokter?.id;


        if (!dokterId) {

            labError.value =
                'Dokter pada pendaftaran tidak ditemukan.';

            showNotification(
                'error',
                'Permintaan Lab Gagal',
                'Dokter pada pendaftaran tidak ditemukan.'
            );

            return;

        }


        const response = await axios.post(
            '/api/lab-permintaans',
            {

                pendaftaran_id:
                    pendaftaran.value.id,

                dokter_id:
                    dokterId,

                tanggal_permintaan:
                    getToday(),

                catatan:
                    labCatatan.value || null,

                pemeriksaan_ids:
                    selectedLabIds.value,

            }
        );


        console.log(
            'PERMINTAAN LAB BERHASIL:',
            response.data
        );


        labSuccess.value =

            response.data?.message ||

            'Permintaan laboratorium berhasil dibuat.';


        showNotification(
            'success',
            'Permintaan Laboratorium Berhasil',
            `Permintaan pemeriksaan laboratorium untuk pasien ${
                pendaftaran.value.pasien?.nama || ''
            } berhasil dikirim.`,
            5000
        );


        setTimeout(() => {

            tutupModalLab();

        }, 1000);


    } catch (err) {

        console.error(
            'GAGAL MEMBUAT PERMINTAAN LAB:',
            err
        );


        console.error(
            'RESPONSE:',
            err.response?.data
        );


        if (err.response?.status === 422) {

            const errors =
                err.response?.data?.errors;


            if (errors) {

                labError.value =

                    Object.entries(errors)

                        .map(
                            ([field, messages]) => {

                                return `${field}: ${messages.join(', ')}`;

                            }
                        )

                        .join(' | ');

            } else {

                labError.value =

                    err.response?.data?.message ||

                    'Data permintaan laboratorium tidak valid.';

            }


        } else {

            labError.value =

                err.response?.data?.message ||

                'Gagal membuat permintaan laboratorium.';

        }


        showNotification(
            'error',
            'Permintaan Lab Gagal',
            labError.value
        );


    } finally {

        savingLab.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| GET HASIL LAB
|--------------------------------------------------------------------------
|
| BAGIAN INI DIPERBAIKI
|--------------------------------------------------------------------------
*/

const getHasilLab = async () => {
    if (!pendaftaranId.value) {
        hasilLabs.value = [];
        return;
    }

    try {
        loadingHasilLab.value = true;
        hasilLabError.value = '';

        const response = await axios.get(
            `/api/pendaftarans/${pendaftaranId.value}/hasil-lab`
        );

        console.log(
            '================================================'
        );

        console.log(
            'RESPONSE HASIL LAB:',
            response.data
        );

        console.log(
            '================================================'
        );

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA UTAMA
        |--------------------------------------------------------------------------
        */

        const labs = Array.isArray(
            response.data?.data
        )
            ? response.data.data
            : [];

        /*
        |--------------------------------------------------------------------------
        | FLATTEN
        |--------------------------------------------------------------------------
        |
        | Backend:
        |
        | LAB
        |   └── pemeriksaan
        |        └── hasil
        |
        | Kita ubah menjadi:
        |
        | [
        |   {
        |      no_lab,
        |      pemeriksaan,
        |      hasil
        |   }
        | ]
        |
        */

        const hasilTerverifikasi = [];

        labs.forEach((lab) => {
            const pemeriksaanList =
                Array.isArray(
                    lab.pemeriksaan
                )
                    ? lab.pemeriksaan
                    : [];

            pemeriksaanList.forEach(
                (item) => {
                    /*
                    |--------------------------------------------------------------------------
                    | HASIL PEMERIKSAAN
                    |--------------------------------------------------------------------------
                    */

                    const hasil =
                        item.hasil || null;

                    /*
                    |--------------------------------------------------------------------------
                    | HANYA TAMPILKAN HASIL YANG
                    | SUDAH DIVERIFIKASI
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !hasil ||
                        !hasil.tanggal_verifikasi
                    ) {
                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN DATA YANG SUDAH DINORMALISASI
                    |--------------------------------------------------------------------------
                    */

                    hasilTerverifikasi.push({
                        id:
                            hasil.id ??
                            item.detail_id,

                        hasil_id:
                            hasil.id ??
                            null,

                        detail_id:
                            item.detail_id ??
                            null,

                        no_lab:
                            lab.no_lab ??
                            '-',

                        tanggal_permintaan:
                            lab.tanggal_permintaan ??
                            null,

                        status:
                            lab.status ??
                            'DIVERIFIKASI',

                        pemeriksaan:
                            item.pemeriksaan ??
                            {},

                        hasil:
                            hasil,
                    });
                }
            );
        });

        /*
        |--------------------------------------------------------------------------
        | SIMPAN KE STATE
        |--------------------------------------------------------------------------
        */

        hasilLabs.value =
            hasilTerverifikasi;

        console.log(
            'HASIL LAB TERVERIFIKASI:',
            hasilLabs.value
        );

    } catch (err) {
        console.error(
            'Gagal mengambil hasil laboratorium:',
            err
        );

        console.error(
            'ERROR RESPONSE:',
            err.response?.data
        );

        hasilLabs.value = [];

        hasilLabError.value =
            err.response?.data?.message ||
            'Gagal mengambil hasil laboratorium.';

    } finally {
        loadingHasilLab.value = false;
    }
};


/*
|--------------------------------------------------------------------------
| REFRESH HASIL LAB
|--------------------------------------------------------------------------
*/

const refreshHasilLab = async () => {

    await getHasilLab();


    showNotification(
        'success',
        'Hasil Laboratorium',
        'Data hasil laboratorium berhasil diperbarui.',
        2500
    );

};


/*
|--------------------------------------------------------------------------
| HELPER HASIL LAB
|--------------------------------------------------------------------------
*/

const getNamaPemeriksaanLab = (item) => {
    return (
        item?.pemeriksaan?.nama ||
        '-'
    );
};

const getKodePemeriksaanLab = (item) => {
    return (
        item?.pemeriksaan?.kode ||
        '-'
    );
};

const getNilaiHasilLab = (item) => {
    return (
        item?.hasil?.hasil ??
        '-'
    );
};

const getSatuanHasilLab = (item) => {
    return (
        item?.pemeriksaan?.satuan ||
        '-'
    );
};

const getNilaiRujukanLab = (item) => {
    return (
        item?.pemeriksaan?.nilai_rujukan ||
        '-'
    );
};

const getCatatanHasilLab = (item) => {
    return (
        item?.hasil?.catatan ||
        ''
    );
};

const getStatusHasilLab = (item) => {
    if (
        item?.hasil?.tanggal_verifikasi
    ) {
        return 'DIVERIFIKASI';
    }

    return 'BELUM DIVERIFIKASI';
};


/*
|--------------------------------------------------------------------------
| GET DETAIL PEMERIKSAAN
|--------------------------------------------------------------------------
*/

const getPemeriksaan = async () => {

    if (!pendaftaranId.value) {

        return;

    }


    try {

        loading.value = true;

        error.value = '';

        success.value = '';

        pendaftaran.value = null;

        pemeriksaan.value = null;

        hasilLabs.value = [];

        resetForm();


        console.log(
            'Mengambil pemeriksaan ID:',
            pendaftaranId.value
        );


        /*
        |--------------------------------------------------------------------------
        | PEMERIKSAAN
        |--------------------------------------------------------------------------
        */

        const response = await axios.get(

            `/api/pendaftarans/${pendaftaranId.value}/pemeriksaan`

        );


        console.log(
            'RESPONSE PEMERIKSAAN:',
            response.data
        );


        pendaftaran.value =

            response.data?.pendaftaran ||

            response.data?.data?.pendaftaran ||

            null;


        pemeriksaan.value =

            response.data?.pemeriksaan ||

            response.data?.data?.pemeriksaan ||

            null;


        console.log(
            'PENDAFTARAN:',
            pendaftaran.value
        );


        console.log(
            'PEMERIKSAAN:',
            pemeriksaan.value
        );


        /*
        |--------------------------------------------------------------------------
        | FORM
        |--------------------------------------------------------------------------
        */

        if (pemeriksaan.value) {

            form.value = {

                tekanan_darah:
                    pemeriksaan.value.tekanan_darah ?? '',

                suhu:
                    pemeriksaan.value.suhu ?? '',

                berat_badan:
                    pemeriksaan.value.berat_badan ?? '',

                tinggi_badan:
                    pemeriksaan.value.tinggi_badan ?? '',

                nadi:
                    pemeriksaan.value.nadi ?? '',

                respirasi:
                    pemeriksaan.value.respirasi ?? '',

                diagnosis:
                    pemeriksaan.value.diagnosis ?? '',

                tindakan:
                    pemeriksaan.value.tindakan ?? '',

                catatan:
                    pemeriksaan.value.catatan ?? '',

            };

        }


        /*
        |--------------------------------------------------------------------------
        | HASIL LAB
        |--------------------------------------------------------------------------
        */

        await getHasilLab();


    } catch (err) {

        console.error(
            'Gagal mengambil pemeriksaan:',
            err
        );


        if (
            err.response?.status === 404
        ) {

            error.value =
                'Data pendaftaran tidak ditemukan.';

        } else {

            error.value =

                err.response?.data?.message ||

                'Gagal mengambil data pemeriksaan.';

        }


    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| MULAI PEMERIKSAAN
|--------------------------------------------------------------------------
*/

const mulaiPemeriksaan = (item) => {

    router.push(
        `/pendaftarans/${item.id}/pemeriksaan`
    );

};


/*
|--------------------------------------------------------------------------
| SIMPAN PEMERIKSAAN
|--------------------------------------------------------------------------
*/

const simpanPemeriksaan = async () => {

    try {

        saving.value = true;

        error.value = '';

        success.value = '';


        console.log(
            'DATA PEMERIKSAAN:',
            form.value
        );


        const response = await axios.post(

            `/api/pendaftarans/${pendaftaranId.value}/pemeriksaan`,

            form.value

        );


        console.log(
            'PEMERIKSAAN BERHASIL:',
            response.data
        );


        success.value =

            response.data?.message ||

            'Pemeriksaan berhasil disimpan.';


        showNotification(
            'success',
            'Pemeriksaan Berhasil Disimpan',
            'Data pemeriksaan pasien berhasil disimpan.',
            4000
        );


        await getPemeriksaan();


    } catch (err) {

        console.error(
            'GAGAL SIMPAN PEMERIKSAAN:',
            err
        );


        console.error(
            'STATUS:',
            err.response?.status
        );


        console.error(
            'RESPONSE:',
            err.response?.data
        );


        if (
            err.response?.status === 422
        ) {

            const errors =
                err.response?.data?.errors;


            if (errors) {

                error.value =

                    Object.entries(errors)

                        .map(
                            ([field, messages]) => {

                                return `${field}: ${messages.join(', ')}`;

                            }
                        )

                        .join(' | ');

            } else {

                error.value =

                    err.response?.data?.message ||

                    'Data pemeriksaan tidak valid.';

            }

        } else {

            error.value =

                err.response?.data?.message ||

                'Gagal menyimpan pemeriksaan.';

        }


        showNotification(
            'error',
            'Pemeriksaan Gagal',
            error.value
        );


    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| KEMBALI
|--------------------------------------------------------------------------
*/

const kembali = () => {

    router.push('/pemeriksaan');

};


/*
|--------------------------------------------------------------------------
| STATUS LABEL
|--------------------------------------------------------------------------
*/

const statusLabel = (status) => {

    const labels = {

        MENUNGGU: 'Menunggu',

        DIPANGGIL: 'Dipanggil',

        DIPERIKSA: 'Diperiksa',

        SELESAI: 'Selesai',

        BATAL: 'Batal',

    };


    return labels[status] || status;

};


/*
|--------------------------------------------------------------------------
| STATUS CLASS
|--------------------------------------------------------------------------
*/

const statusClass = (status) => {

    const classes = {

        MENUNGGU:
            'bg-yellow-100 text-yellow-700',

        DIPANGGIL:
            'bg-blue-100 text-blue-700',

        DIPERIKSA:
            'bg-purple-100 text-purple-700',

        SELESAI:
            'bg-green-100 text-green-700',

        BATAL:
            'bg-red-100 text-red-700',

    };


    return (

        classes[status] ||

        'bg-gray-100 text-gray-600'

    );

};


/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    if (isDetail.value) {

        /*
        | getPemeriksaan()
        | sudah otomatis memanggil getHasilLab()
        */

        await getPemeriksaan();

    } else {

        await getPendaftarans();

    }

});


/*
|--------------------------------------------------------------------------
| ROUTE WATCH
|--------------------------------------------------------------------------
*/

watch(

    () => route.params.id,

    async (newId, oldId) => {

        if (newId === oldId) {

            return;

        }


        if (newId) {

            await getPemeriksaan();

        } else {

            await getPendaftarans();

            hasilLabs.value = [];

        }

    }

);

</script>


<template>

<div class="space-y-6">


    <!-- ===================================================== -->
    <!-- NOTIFICATION -->
    <!-- ===================================================== -->

    <Transition name="notification">

        <div
            v-if="notification.show"
            class="fixed
                   right-5
                   top-5
                   z-[9999]
                   w-[calc(100%-2.5rem)]
                   max-w-md"
        >

            <div
                class="overflow-hidden
                       rounded-2xl
                       border
                       bg-white
                       shadow-2xl"
                :class="{

                    'border-green-200':
                        notification.type === 'success',

                    'border-red-200':
                        notification.type === 'error',

                    'border-yellow-200':
                        notification.type === 'warning',

                }"
            >

                <div class="flex gap-4 p-4">

                    <div
                        class="flex h-11 w-11
                               shrink-0
                               items-center
                               justify-center
                               rounded-full
                               text-xl"
                        :class="{

                            'bg-green-100':
                                notification.type === 'success',

                            'bg-red-100':
                                notification.type === 'error',

                            'bg-yellow-100':
                                notification.type === 'warning',

                        }"
                    >

                        <span
                            v-if="
                                notification.type ===
                                'success'
                            "
                        >
                            ✓
                        </span>

                        <span
                            v-else-if="
                                notification.type ===
                                'error'
                            "
                        >
                            !
                        </span>

                        <span v-else>
                            ⚠
                        </span>

                    </div>


                    <div class="min-w-0 flex-1">

                        <h3
                            class="font-semibold"
                            :class="{

                                'text-green-800':
                                    notification.type ===
                                    'success',

                                'text-red-800':
                                    notification.type ===
                                    'error',

                                'text-yellow-800':
                                    notification.type ===
                                    'warning',

                            }"
                        >

                            {{ notification.title }}

                        </h3>


                        <p
                            class="mt-1
                                   text-sm
                                   text-gray-600"
                        >

                            {{ notification.message }}

                        </p>

                    </div>


                    <button
                        @click="closeNotification"
                        type="button"
                        class="shrink-0
                               text-gray-400
                               hover:text-gray-700"
                    >

                        ✕

                    </button>

                </div>


                <div
                    class="h-1"
                    :class="{

                        'bg-green-500':
                            notification.type === 'success',

                        'bg-red-500':
                            notification.type === 'error',

                        'bg-yellow-500':
                            notification.type === 'warning',

                    }"
                ></div>

            </div>

        </div>

    </Transition>


    <!-- ===================================================== -->
    <!-- DAFTAR PEMERIKSAAN -->
    <!-- ===================================================== -->

    <template v-if="!isDetail">

        <div
            class="flex flex-col gap-4
                   md:flex-row
                   md:items-center
                   md:justify-between"
        >

            <div>

                <h1
                    class="text-2xl
                           font-bold
                           text-gray-800"
                >

                    Pemeriksaan Pasien

                </h1>


                <p
                    class="mt-1
                           text-sm
                           text-gray-500"
                >

                    Daftar pasien yang siap
                    dilakukan pemeriksaan.

                </p>

            </div>


            <button
                @click="getPendaftarans"
                :disabled="loading"
                class="rounded-lg
                       bg-blue-600
                       px-4 py-2
                       text-sm
                       font-semibold
                       text-white
                       hover:bg-blue-700
                       disabled:opacity-50"
            >

                {{
                    loading
                        ? 'Memuat...'
                        : '↻ Refresh'
                }}

            </button>

        </div>


        <div
            v-if="error"
            class="rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   p-4
                   text-red-700"
        >

            {{ error }}

        </div>


        <div
            class="overflow-hidden
                   rounded-2xl
                   border
                   border-gray-200
                   bg-white"
        >

            <div
                v-if="loading"
                class="p-10
                       text-center
                       text-gray-500"
            >

                Memuat pasien...

            </div>


            <div
                v-else-if="
                    pasienUntukDiperiksa.length === 0
                "
                class="p-10
                       text-center"
            >

                <div class="mb-4 text-5xl">
                    🩺
                </div>


                <h3
                    class="font-semibold
                           text-gray-800"
                >

                    Belum ada pasien

                </h3>


                <p
                    class="mt-1
                           text-sm
                           text-gray-500"
                >

                    Belum ada pasien yang
                    siap diperiksa.

                </p>


                <button
                    @click="router.push('/antrian')"
                    class="mt-5
                           rounded-lg
                           bg-blue-600
                           px-4 py-2
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-blue-700"
                >

                    Lihat Antrian

                </button>

            </div>


            <div
                v-else
                class="overflow-x-auto"
            >

                <table class="w-full text-sm">

                    <thead
                        class="border-b
                               bg-gray-50"
                    >

                        <tr>

                            <th
                                class="px-5 py-4
                                       text-left
                                       font-semibold
                                       text-gray-500"
                            >
                                Antrean
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       font-semibold
                                       text-gray-500"
                            >
                                Pasien
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       font-semibold
                                       text-gray-500"
                            >
                                Poli
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       font-semibold
                                       text-gray-500"
                            >
                                Dokter
                            </th>


                            <th
                                class="px-5 py-4
                                       text-left
                                       font-semibold
                                       text-gray-500"
                            >
                                Status
                            </th>


                            <th
                                class="px-5 py-4
                                       text-right
                                       font-semibold
                                       text-gray-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="
                                item in
                                pasienUntukDiperiksa
                            "
                            :key="item.id"
                            class="border-b
                                   border-gray-100
                                   hover:bg-gray-50"
                        >

                            <td class="px-5 py-5">

                                <span
                                    class="text-xl
                                           font-bold
                                           text-blue-600"
                                >

                                    {{ item.no_antrian }}

                                </span>

                            </td>


                            <td class="px-5 py-5">

                                <p
                                    class="font-semibold
                                           text-gray-800"
                                >

                                    {{
                                        item.pasien?.nama ||
                                        '-'
                                    }}

                                </p>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-gray-500"
                                >

                                    RM:
                                    {{
                                        item.pasien?.no_rm ||
                                        '-'
                                    }}

                                </p>

                            </td>


                            <td class="px-5 py-5">

                                {{
                                    item.poli?.nama ||
                                    '-'
                                }}

                            </td>


                            <td class="px-5 py-5">

                                {{
                                    item.dokter?.nama ||
                                    '-'
                                }}

                            </td>


                            <td class="px-5 py-5">

                                <span
                                    class="rounded-full
                                           px-3 py-1
                                           text-xs
                                           font-semibold"
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


                            <td
                                class="px-5 py-5
                                       text-right"
                            >

                                <button
                                    @click="
                                        mulaiPemeriksaan(item)
                                    "
                                    class="rounded-lg
                                           bg-purple-600
                                           px-4 py-2
                                           text-xs
                                           font-semibold
                                           text-white
                                           hover:bg-purple-700"
                                >

                                    🩺

                                    {{
                                        item.status ===
                                        'DIPERIKSA'
                                            ? 'Buka Pemeriksaan'
                                            : 'Periksa'
                                    }}

                                </button>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </template>


    <!-- ===================================================== -->
    <!-- DETAIL PEMERIKSAAN -->
    <!-- ===================================================== -->

    <template v-else>

        <div>

            <button
                @click="kembali"
                class="mb-2
                       text-sm
                       font-medium
                       text-blue-600
                       hover:text-blue-800"
            >

                ← Kembali ke Pemeriksaan

            </button>


            <h1
                class="text-2xl
                       font-bold
                       text-gray-800"
            >

                Pemeriksaan Pasien

            </h1>

        </div>


        <div
            v-if="error"
            class="rounded-xl
                   border
                   border-red-200
                   bg-red-50
                   p-4
                   text-red-700"
        >

            {{ error }}

        </div>


        <div
            v-if="loading"
            class="rounded-2xl
                   border
                   bg-white
                   p-10
                   text-center
                   text-gray-500"
        >

            Memuat data pasien...

        </div>


        <template
            v-else-if="pendaftaran"
        >


            <!-- ================================================= -->
            <!-- DATA PASIEN -->
            <!-- ================================================= -->

            <div
                class="rounded-2xl
                       border
                       border-gray-200
                       bg-white
                       p-6"
            >

                <div
                    class="flex
                           flex-col
                           gap-5
                           md:flex-row
                           md:items-center
                           md:justify-between"
                >

                    <div>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Pasien
                        </p>


                        <h2
                            class="text-xl
                                   font-bold
                                   text-gray-800"
                        >

                            {{
                                pendaftaran.pasien?.nama ||
                                '-'
                            }}

                        </h2>


                        <p
                            class="mt-1
                                   text-sm
                                   text-gray-500"
                        >

                            No. RM:
                            {{
                                pendaftaran.pasien?.no_rm ||
                                '-'
                            }}

                        </p>

                    </div>


                    <div
                        class="flex
                               flex-wrap
                               gap-3"
                    >

                        <div
                            class="rounded-xl
                                   bg-blue-50
                                   px-4 py-3"
                        >

                            <p
                                class="text-xs
                                       text-blue-500"
                            >
                                No. Antrean
                            </p>


                            <p
                                class="text-lg
                                       font-bold
                                       text-blue-700"
                            >

                                {{
                                    pendaftaran.no_antrian
                                }}

                            </p>

                        </div>


                        <div
                            class="rounded-xl
                                   bg-purple-50
                                   px-4 py-3"
                        >

                            <p
                                class="text-xs
                                       text-purple-500"
                            >
                                Poli
                            </p>


                            <p
                                class="text-sm
                                       font-bold
                                       text-purple-700"
                            >

                                {{
                                    pendaftaran.poli?.nama ||
                                    '-'
                                }}

                            </p>

                        </div>


                        <div
                            class="rounded-xl
                                   bg-gray-50
                                   px-4 py-3"
                        >

                            <p
                                class="text-xs
                                       text-gray-500"
                            >
                                Dokter
                            </p>


                            <p
                                class="text-sm
                                       font-bold
                                       text-gray-700"
                            >

                                {{
                                    pendaftaran.dokter?.nama ||
                                    '-'
                                }}

                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- HASIL PEMERIKSAAN -->
            <!-- ================================================= -->

            <div
                class="rounded-2xl
                       border
                       border-gray-200
                       bg-white
                       p-6"
            >

                <div class="mb-6">

                    <h2
                        class="text-lg
                               font-bold
                               text-gray-800"
                    >

                        Hasil Pemeriksaan

                    </h2>


                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >

                        Masukkan hasil pemeriksaan
                        pasien.

                    </p>

                </div>


                <div
                    class="grid
                           grid-cols-1
                           gap-5
                           md:grid-cols-2
                           lg:grid-cols-3"
                >

                    <div>

                        <label class="label">
                            Tekanan Darah
                        </label>

                        <input
                            v-model="form.tekanan_darah"
                            type="text"
                            placeholder="120/80"
                            class="input"
                        >

                    </div>


                    <div>

                        <label class="label">
                            Suhu (°C)
                        </label>

                        <input
                            v-model="form.suhu"
                            type="number"
                            step="0.1"
                            placeholder="36.5"
                            class="input"
                        >

                    </div>


                    <div>

                        <label class="label">
                            Berat Badan (kg)
                        </label>

                        <input
                            v-model="form.berat_badan"
                            type="number"
                            step="0.1"
                            placeholder="60"
                            class="input"
                        >

                    </div>


                    <div>

                        <label class="label">
                            Tinggi Badan (cm)
                        </label>

                        <input
                            v-model="form.tinggi_badan"
                            type="number"
                            step="0.1"
                            placeholder="170"
                            class="input"
                        >

                    </div>


                    <div>

                        <label class="label">
                            Nadi (bpm)
                        </label>

                        <input
                            v-model="form.nadi"
                            type="number"
                            placeholder="80"
                            class="input"
                        >

                    </div>


                    <div>

                        <label class="label">
                            Respirasi (x/menit)
                        </label>

                        <input
                            v-model="form.respirasi"
                            type="number"
                            placeholder="20"
                            class="input"
                        >

                    </div>

                </div>


                <div class="mt-6">

                    <label class="label">
                        Diagnosis
                    </label>

                    <textarea
                        v-model="form.diagnosis"
                        rows="4"
                        placeholder="Masukkan diagnosis pasien..."
                        class="textarea"
                    ></textarea>

                </div>


                <div class="mt-5">

                    <label class="label">
                        Tindakan
                    </label>

                    <textarea
                        v-model="form.tindakan"
                        rows="4"
                        placeholder="Masukkan tindakan yang dilakukan..."
                        class="textarea"
                    ></textarea>

                </div>


                <div class="mt-5">

                    <label class="label">
                        Catatan
                    </label>

                    <textarea
                        v-model="form.catatan"
                        rows="4"
                        placeholder="Catatan tambahan..."
                        class="textarea"
                    ></textarea>

                </div>


                <div
                    v-if="success"
                    class="mt-5
                           rounded-xl
                           border
                           border-green-200
                           bg-green-50
                           p-4
                           text-green-700"
                >

                    {{ success }}

                </div>


                <div
                    class="mt-6
                           flex
                           flex-col
                           gap-3
                           sm:flex-row
                           sm:justify-end"
                >

                    <button
                        @click="bukaModalLab"
                        type="button"
                        class="rounded-xl
                               bg-purple-600
                               px-5 py-3
                               font-semibold
                               text-white
                               hover:bg-purple-700"
                    >

                        🧪 Sarankan Pemeriksaan Lab

                    </button>


                    <button
                        @click="kembali"
                        type="button"
                        class="rounded-xl
                               border
                               border-gray-300
                               px-5 py-3
                               text-gray-700
                               hover:bg-gray-50"
                    >

                        Kembali

                    </button>


                    <button
                        @click="simpanPemeriksaan"
                        :disabled="saving"
                        type="button"
                        class="rounded-xl
                               bg-blue-600
                               px-5 py-3
                               font-semibold
                               text-white
                               hover:bg-blue-700
                               disabled:opacity-50"
                    >

                        {{
                            saving
                                ? 'Menyimpan...'
                                : '💾 Simpan Pemeriksaan'
                        }}

                    </button>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- HASIL LAB -->
            <!-- ================================================= -->

            <div
                class="rounded-2xl
                       border
                       border-gray-200
                       bg-white
                       p-6"
            >

                <div
                    class="mb-6
                           flex
                           flex-col
                           gap-4
                           md:flex-row
                           md:items-center
                           md:justify-between"
                >

                    <div>

                        <div
                            class="flex
                                   items-center
                                   gap-3"
                        >

                            <div
                                class="flex
                                       h-11
                                       w-11
                                       items-center
                                       justify-center
                                       rounded-xl
                                       bg-blue-50
                                       text-xl"
                            >

                                🧪

                            </div>


                            <div>

                                <h2
                                    class="text-lg
                                           font-bold
                                           text-gray-800"
                                >

                                    Hasil Laboratorium

                                </h2>


                                <p
                                    class="mt-1
                                           text-sm
                                           text-gray-500"
                                >

                                    Hasil pemeriksaan laboratorium
                                    yang telah diverifikasi oleh
                                    petugas lab.

                                </p>

                            </div>

                        </div>

                    </div>


                    <button
                        @click="refreshHasilLab"
                        :disabled="loadingHasilLab"
                        type="button"
                        class="inline-flex
                               items-center
                               justify-center
                               gap-2
                               rounded-xl
                               border
                               border-gray-300
                               bg-white
                               px-4
                               py-2.5
                               text-sm
                               font-semibold
                               text-gray-700
                               hover:bg-gray-50
                               disabled:cursor-not-allowed
                               disabled:opacity-50"
                    >

                        <span
                            :class="{
                                'animate-spin':
                                    loadingHasilLab
                            }"
                        >

                            ↻

                        </span>


                        {{
                            loadingHasilLab
                                ? 'Memuat...'
                                : 'Refresh'
                        }}

                    </button>

                </div>


                <!-- ERROR -->

                <div
                    v-if="hasilLabError"
                    class="mb-5
                           rounded-xl
                           border
                           border-red-200
                           bg-red-50
                           p-4
                           text-sm
                           text-red-700"
                >

                    {{ hasilLabError }}

                </div>


                <!-- LOADING -->

                <div
                    v-if="loadingHasilLab"
                    class="rounded-xl
                           border
                           border-gray-200
                           bg-gray-50
                           p-10
                           text-center"
                >

                    <div
                        class="mb-3
                               text-4xl"
                    >
                        🧪
                    </div>


                    <p
                        class="text-sm
                               text-gray-500"
                    >

                        Memuat hasil laboratorium...

                    </p>

                </div>


                <!-- EMPTY -->

                <div
                    v-else-if="
                        hasilLabs.length === 0 &&
                        !hasilLabError
                    "
                    class="rounded-xl
                           border
                           border-dashed
                           border-gray-300
                           bg-gray-50
                           p-10
                           text-center"
                >

                    <div
                        class="mb-3 text-4xl"
                    >
                        🧪
                    </div>


                    <h3
                        class="font-semibold
                               text-gray-700"
                    >

                        Belum Ada Hasil Laboratorium

                    </h3>


                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >

                        Belum ada hasil pemeriksaan laboratorium
                        yang telah diverifikasi.

                    </p>

                </div>


                <!-- HASIL -->

                <div
                    v-else
                    class="space-y-4"
                >

                    <div
                        v-for="
                            (hasil, index) in hasilLabs
                        "
                        :key="
                            hasil.id ||
                            hasil.hasil_id ||
                            index
                        "
                        class="overflow-hidden
                               rounded-2xl
                               border
                               border-gray-200
                               bg-white"
                    >

                        <!-- HEADER -->

                        <div
                            class="flex
                                   flex-col
                                   gap-3
                                   border-b
                                   bg-gray-50
                                   px-5
                                   py-4
                                   md:flex-row
                                   md:items-center
                                   md:justify-between"
                        >

                            <div>

                                <p
                                    class="text-xs
                                           font-medium
                                           uppercase
                                           tracking-wide
                                           text-gray-400"
                                >

                                    Pemeriksaan Laboratorium

                                </p>


                                <h3
                                    class="mt-1
                                           font-bold
                                           text-gray-800"
                                >

                                    {{
                                        getNamaPemeriksaanLab(
                                            hasil
                                        )
                                    }}

                                </h3>


                                <p
                                    class="mt-1
                                           text-xs
                                           text-gray-500"
                                >

                                    Kode:

                                    {{
                                        getKodePemeriksaanLab(
                                            hasil
                                        )
                                    }}

                                </p>

                            </div>


                            <span
                                class="inline-flex
                                       w-fit
                                       items-center
                                       gap-2
                                       rounded-full
                                       bg-green-100
                                       px-3
                                       py-1.5
                                       text-xs
                                       font-semibold
                                       text-green-700"
                            >

                                ✓

                                {{
                                    getStatusHasilLab(
                                        hasil
                                    )
                                }}

                            </span>

                        </div>


                        <!-- BODY -->

                        <div class="p-5">

                            <div
                                class="grid
                                       grid-cols-1
                                       gap-4
                                       md:grid-cols-3"
                            >

                                <!-- HASIL -->

                                <div
                                    class="rounded-xl
                                           bg-blue-50
                                           p-4"
                                >

                                    <p
                                        class="text-xs
                                               font-medium
                                               text-blue-500"
                                    >

                                        Hasil Pemeriksaan

                                    </p>


                                    <p
                                        class="mt-2
                                               text-2xl
                                               font-bold
                                               text-blue-700"
                                    >

                                        {{
                                            getNilaiHasilLab(
                                                hasil
                                            )
                                        }}

                                    </p>

                                </div>


                                <!-- SATUAN -->

                                <div
                                    class="rounded-xl
                                           bg-gray-50
                                           p-4"
                                >

                                    <p
                                        class="text-xs
                                               font-medium
                                               text-gray-500"
                                    >

                                        Satuan

                                    </p>


                                    <p
                                        class="mt-2
                                               text-lg
                                               font-semibold
                                               text-gray-700"
                                    >

                                        {{
                                            getSatuanHasilLab(
                                                hasil
                                            )
                                        }}

                                    </p>

                                </div>


                                <!-- NILAI RUJUKAN -->

                                <div
                                    class="rounded-xl
                                           bg-purple-50
                                           p-4"
                                >

                                    <p
                                        class="text-xs
                                               font-medium
                                               text-purple-500"
                                    >

                                        Nilai Rujukan

                                    </p>


                                    <p
                                        class="mt-2
                                               text-sm
                                               font-semibold
                                               text-purple-700"
                                    >

                                        {{
                                            getNilaiRujukanLab(
                                                hasil
                                            )
                                        }}

                                    </p>

                                </div>

                            </div>


                            <!-- CATATAN -->

                            <div
                                v-if="
                                    getCatatanHasilLab(
                                        hasil
                                    )
                                "
                                class="mt-4
                                       rounded-xl
                                       border
                                       border-yellow-200
                                       bg-yellow-50
                                       p-4"
                            >

                                <p
                                    class="text-xs
                                           font-semibold
                                           text-yellow-700"
                                >

                                    Catatan / Keterangan

                                </p>


                                <p
                                    class="mt-1
                                           text-sm
                                           text-yellow-800"
                                >

                                    {{
                                        getCatatanHasilLab(
                                            hasil
                                        )
                                    }}

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </template>


        <!-- DATA TIDAK ADA -->

        <div
            v-else
            class="rounded-2xl
                   border
                   bg-white
                   p-10
                   text-center"
        >

            <div class="mb-4 text-5xl">
                ⚠️
            </div>


            <h3
                class="font-semibold
                       text-gray-800"
            >

                Data pendaftaran tidak ditemukan

            </h3>


            <p
                class="mt-2
                       text-sm
                       text-gray-500"
            >

                ID pendaftaran:
                {{ pendaftaranId }}

            </p>


            <button
                @click="kembali"
                class="mt-5
                       rounded-lg
                       bg-blue-600
                       px-4 py-2
                       text-white"
            >

                Kembali

            </button>

        </div>

    </template>


    <!-- ===================================================== -->
    <!-- MODAL LAB -->
    <!-- ===================================================== -->

    <Transition name="modal">

        <div
            v-if="showLabModal"
            class="fixed
                   inset-0
                   z-[9998]
                   flex
                   items-center
                   justify-center
                   bg-black/50
                   p-4"
        >

            <div
                class="w-full
                       max-w-2xl
                       overflow-hidden
                       rounded-2xl
                       bg-white
                       shadow-2xl"
            >

                <!-- HEADER -->

                <div
                    class="flex
                           items-center
                           justify-between
                           border-b
                           px-6 py-5"
                >

                    <div>

                        <h2
                            class="text-xl
                                   font-bold
                                   text-gray-800"
                        >

                            🧪 Sarankan Pemeriksaan Lab

                        </h2>


                        <p
                            class="mt-1
                                   text-sm
                                   text-gray-500"
                        >

                            Pilih pemeriksaan laboratorium
                            yang diperlukan pasien.

                        </p>

                    </div>


                    <button
                        @click="tutupModalLab"
                        :disabled="savingLab"
                        type="button"
                        class="text-2xl
                               text-gray-400
                               hover:text-gray-700
                               disabled:opacity-50"
                    >

                        ✕

                    </button>

                </div>


                <!-- BODY -->

                <div
                    class="max-h-[65vh]
                           overflow-y-auto
                           p-6"
                >

                    <!-- ERROR -->

                    <div
                        v-if="labError"
                        class="mb-5
                               rounded-xl
                               border
                               border-red-200
                               bg-red-50
                               p-4
                               text-sm
                               text-red-700"
                    >

                        {{ labError }}

                    </div>


                    <!-- SUCCESS -->

                    <div
                        v-if="labSuccess"
                        class="mb-5
                               rounded-xl
                               border
                               border-green-200
                               bg-green-50
                               p-4
                               text-sm
                               text-green-700"
                    >

                        {{ labSuccess }}

                    </div>


                    <!-- LOADING -->

                    <div
                        v-if="loadingLab"
                        class="py-10
                               text-center
                               text-gray-500"
                    >

                        Memuat pemeriksaan
                        laboratorium...

                    </div>


                    <!-- EMPTY -->

                    <div
                        v-else-if="
                            labPemeriksaans.length === 0
                        "
                        class="py-10
                               text-center
                               text-gray-500"
                    >

                        <div
                            class="mb-3 text-4xl"
                        >
                            🧪
                        </div>


                        <p>

                            Belum ada pemeriksaan
                            laboratorium aktif.

                        </p>

                    </div>


                    <!-- LIST -->

                    <div
                        v-else
                        class="space-y-3"
                    >

                        <label
                            v-for="
                                lab in
                                labPemeriksaans
                            "
                            :key="lab.id"
                            class="flex
                                   cursor-pointer
                                   items-start
                                   gap-4
                                   rounded-xl
                                   border
                                   border-gray-200
                                   p-4
                                   transition
                                   hover:border-purple-400
                                   hover:bg-purple-50"
                            :class="{

                                'border-purple-500 bg-purple-50':
                                    selectedLabIds.includes(
                                        lab.id
                                    )

                            }"
                        >

                            <input
                                v-model="
                                    selectedLabIds
                                "
                                :value="lab.id"
                                type="checkbox"
                                class="mt-1
                                       h-5 w-5
                                       rounded
                                       border-gray-300
                                       text-purple-600
                                       focus:ring-purple-500"
                            >


                            <div class="flex-1">

                                <div
                                    class="flex
                                           flex-col
                                           gap-1
                                           sm:flex-row
                                           sm:items-center
                                           sm:justify-between"
                                >

                                    <div>

                                        <p
                                            class="font-semibold
                                                   text-gray-800"
                                        >

                                            {{ lab.nama }}

                                        </p>


                                        <p
                                            class="mt-1
                                                   text-xs
                                                   text-gray-500"
                                        >

                                            Kode:
                                            {{ lab.kode || '-' }}

                                            <span
                                                v-if="
                                                    lab.kategori
                                                "
                                            >

                                                •

                                                {{
                                                    lab.kategori
                                                }}

                                            </span>

                                        </p>

                                    </div>


                                    <div
                                        class="text-sm
                                               font-semibold
                                               text-purple-600"
                                    >

                                        Rp

                                        {{
                                            Number(
                                                lab.harga || 0
                                            ).toLocaleString(
                                                'id-ID'
                                            )
                                        }}

                                    </div>

                                </div>


                                <p
                                    v-if="
                                        lab.nilai_rujukan
                                    "
                                    class="mt-2
                                           text-xs
                                           text-gray-500"
                                >

                                    Nilai rujukan:

                                    {{
                                        lab.nilai_rujukan
                                    }}

                                </p>

                            </div>

                        </label>

                    </div>


                    <!-- CATATAN -->

                    <div class="mt-6">

                        <label class="label">

                            Catatan Dokter

                            <span
                                class="font-normal
                                       text-gray-400"
                            >

                                (opsional)

                            </span>

                        </label>


                        <textarea
                            v-model="labCatatan"
                            rows="3"
                            placeholder="Contoh: Mohon dilakukan pemeriksaan darah lengkap..."
                            class="textarea"
                        ></textarea>

                    </div>


                    <!-- COUNT -->

                    <div
                        v-if="
                            selectedLabIds.length > 0
                        "
                        class="mt-5
                               rounded-xl
                               bg-purple-50
                               p-4
                               text-sm
                               text-purple-700"
                    >

                        <strong>

                            {{ selectedLabIds.length }}

                        </strong>

                        pemeriksaan laboratorium
                        dipilih.

                    </div>

                </div>


                <!-- FOOTER -->

                <div
                    class="flex
                           flex-col
                           gap-3
                           border-t
                           bg-gray-50
                           px-6 py-4
                           sm:flex-row
                           sm:justify-end"
                >

                    <button
                        @click="tutupModalLab"
                        :disabled="savingLab"
                        type="button"
                        class="rounded-xl
                               border
                               border-gray-300
                               bg-white
                               px-5 py-3
                               font-semibold
                               text-gray-700
                               hover:bg-gray-100
                               disabled:opacity-50"
                    >

                        Batal

                    </button>


                    <button
                        @click="simpanPermintaanLab"
                        :disabled="
                            savingLab ||
                            selectedLabIds.length === 0
                        "
                        type="button"
                        class="rounded-xl
                               bg-purple-600
                               px-5 py-3
                               font-semibold
                               text-white
                               hover:bg-purple-700
                               disabled:cursor-not-allowed
                               disabled:opacity-50"
                    >

                        {{
                            savingLab
                                ? 'Mengirim...'
                                : '🧪 Kirim Permintaan Lab'
                        }}

                    </button>

                </div>

            </div>

        </div>

    </Transition>

</div>

</template>


<style scoped>

.label {

    display: block;

    margin-bottom: 0.5rem;

    font-size: 0.875rem;

    font-weight: 500;

    color: #374151;

}


.input {

    width: 100%;

    padding: 0.75rem 1rem;

    border: 1px solid #d1d5db;

    border-radius: 0.75rem;

    outline: none;

    transition: all 0.2s ease;

}


.input:focus {

    border-color: #3b82f6;

    box-shadow:
        0 0 0 2px
        rgba(
            59,
            130,
            246,
            0.15
        );

}


.textarea {

    width: 100%;

    padding: 0.75rem 1rem;

    border: 1px solid #d1d5db;

    border-radius: 0.75rem;

    outline: none;

    resize: vertical;

    transition: all 0.2s ease;

}


.textarea:focus {

    border-color: #3b82f6;

    box-shadow:
        0 0 0 2px
        rgba(
            59,
            130,
            246,
            0.15
        );

}


.notification-enter-active,
.notification-leave-active {

    transition:
        opacity 0.3s ease,
        transform 0.3s ease;

}


.notification-enter-from,
.notification-leave-to {

    opacity: 0;

    transform:
        translateX(30px);

}


.modal-enter-active,
.modal-leave-active {

    transition:
        opacity 0.2s ease;

}


.modal-enter-from,
.modal-leave-to {

    opacity: 0;

}

</style>
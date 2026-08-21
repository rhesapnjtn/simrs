<template>
    <div class="laboratorium-page">
        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <div class="page-header">
            <div>
                <h1>Laboratorium</h1>
                <p>
                    Kelola permintaan dan hasil pemeriksaan laboratorium.
                </p>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreateModal"
            >
                + Buat Permintaan Lab
            </button>
        </div>

        <!-- ========================================================= -->
        <!-- ALERT -->
        <!-- ========================================================= -->

        <div
            v-if="successMessage"
            class="alert alert-success"
        >
            {{ successMessage }}
        </div>

        <div
            v-if="errorMessage"
            class="alert alert-danger"
        >
            {{ errorMessage }}
        </div>

        <!-- ========================================================= -->
        <!-- FILTER -->
        <!-- ========================================================= -->

        <div class="card filter-card">
            <div class="filter-grid">
                <div class="form-group">
                    <label>Cari No. Lab</label>

                    <input
                        v-model="filters.search"
                        type="text"
                        class="form-control"
                        placeholder="Contoh: LAB-20260819-0001"
                        @keyup.enter="loadLabPermintaans"
                    />
                </div>

                <div class="form-group">
                    <label>Status</label>

                    <select
                        v-model="filters.status"
                        class="form-control"
                        @change="loadLabPermintaans"
                    >
                        <option value="">
                            Semua Status
                        </option>

                        <option value="MENUNGGU">
                            Menunggu
                        </option>

                        <option value="SAMPEL_DIAMBIL">
                            Sampel Diambil
                        </option>

                        <option value="DIPROSES">
                            Diproses
                        </option>

                        <option value="SELESAI">
                            Selesai
                        </option>

                        <option value="DIVERIFIKASI">
                            Diverifikasi
                        </option>

                        <option value="BATAL">
                            Batal
                        </option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="resetFilter"
                    >
                        Reset
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="loadLabPermintaans"
                    >
                        Cari
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- LOADING -->
        <!-- ========================================================= -->

        <div
            v-if="loading"
            class="loading-box"
        >
            Memuat data laboratorium...
        </div>

        <!-- ========================================================= -->
        <!-- TABLE -->
        <!-- ========================================================= -->

        <div
            v-else
            class="card"
        >
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Lab</th>
                            <th>Pasien</th>
                            <th>Dokter</th>
                            <th>Tanggal</th>
                            <th>Pemeriksaan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(lab, index) in labPermintaans"
                            :key="lab.id"
                        >
                            <td>
                                {{ index + 1 }}
                            </td>

                            <td>
                                <strong>
                                    {{ lab.no_lab }}
                                </strong>
                            </td>

                            <td>
                                <div class="patient-name">
                                    {{
                                        lab.pendaftaran?.pasien?.nama ||
                                        '-'
                                    }}
                                </div>

                                <small
                                    v-if="
                                        lab.pendaftaran?.pasien?.no_rm
                                    "
                                >
                                    RM:
                                    {{
                                        lab.pendaftaran.pasien.no_rm
                                    }}
                                </small>
                            </td>

                            <td>
                                {{ lab.dokter?.nama || '-' }}
                            </td>

                            <td>
                                {{
                                    formatDate(
                                        lab.tanggal_permintaan
                                    )
                                }}
                            </td>

                            <td>
                                <span
                                    v-if="
                                        lab.details &&
                                        lab.details.length
                                    "
                                >
                                    {{ lab.details.length }}
                                    pemeriksaan
                                </span>

                                <span v-else>
                                    -
                                </span>
                            </td>

                            <td>
                                <span
                                    class="status-badge"
                                    :class="
                                        statusClass(
                                            lab.status
                                        )
                                    "
                                >
                                    {{
                                        formatStatus(
                                            lab.status
                                        )
                                    }}
                                </span>
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-info"
                                        @click="
                                            openDetailModal(
                                                lab
                                            )
                                        "
                                    >
                                        Detail
                                    </button>

                                    <button
                                        v-if="canEditLab(lab)"
                                        type="button"
                                        class="btn btn-sm btn-warning"
                                        @click="
                                            openEditModal(
                                                lab
                                            )
                                        "
                                    >
                                        Edit
                                    </button>

                                    <button
                                        v-if="canDeleteLab(lab)"
                                        type="button"
                                        class="btn btn-sm btn-danger"
                                        @click="
                                            deleteLab(
                                                lab
                                            )
                                        "
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr
                            v-if="
                                labPermintaans.length === 0
                            "
                        >
                            <td
                                colspan="8"
                                class="empty-state"
                            >
                                Belum ada permintaan laboratorium.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODAL CREATE / EDIT -->
        <!-- ========================================================= -->

        <div
            v-if="showFormModal"
            class="modal-overlay"
            @click.self="closeFormModal"
        >
            <div class="modal modal-lg">
                <div class="modal-header">
                    <div>
                        <h2>
                            {{
                                editingLab
                                    ? 'Edit Permintaan Laboratorium'
                                    : 'Buat Permintaan Laboratorium'
                            }}
                        </h2>

                        <p>
                            Pilih pasien dan pemeriksaan laboratorium.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="modal-close"
                        @click="closeFormModal"
                    >
                        ×
                    </button>
                </div>

                <div class="modal-body">
                    <!-- ================================================= -->
                    <!-- PENDAFTARAN -->
                    <!-- ================================================= -->

                    <div class="form-group">
                        <label>
                            Pasien / Pendaftaran
                            <span class="required">*</span>
                        </label>

                        <select
                            v-model="form.pendaftaran_id"
                            class="form-control"
                        >
                            <option value="">
                                Pilih pasien
                            </option>

                            <option
                                v-for="
                                    pendaftaran in pendaftarans
                                "
                                :key="pendaftaran.id"
                                :value="pendaftaran.id"
                            >
                                {{
                                    pendaftaran.pasien?.nama ||
                                    'Pasien'
                                }}
                                -
                                {{
                                    pendaftaran.no_antrian ||
                                    '-'
                                }}
                                -
                                {{
                                    pendaftaran.poli?.nama ||
                                    'Poli'
                                }}
                            </option>
                        </select>
                    </div>

                    <!-- ================================================= -->
                    <!-- DOKTER -->
                    <!-- ================================================= -->

                    <div class="form-group">
                        <label>
                            Dokter
                            <span class="required">*</span>
                        </label>

                        <select
                            v-model="form.dokter_id"
                            class="form-control"
                        >
                            <option value="">
                                Pilih dokter
                            </option>

                            <option
                                v-for="dokter in dokters"
                                :key="dokter.id"
                                :value="dokter.id"
                            >
                                {{ dokter.nama }}

                                {{
                                    dokter.spesialisasi
                                        ? ` - ${dokter.spesialisasi}`
                                        : ''
                                }}
                            </option>
                        </select>
                    </div>

                    <!-- ================================================= -->
                    <!-- TANGGAL -->
                    <!-- ================================================= -->

                    <div class="form-group">
                        <label>
                            Tanggal Permintaan
                            <span class="required">*</span>
                        </label>

                        <input
                            v-model="
                                form.tanggal_permintaan
                            "
                            type="date"
                            class="form-control"
                        />
                    </div>

                    <!-- ================================================= -->
                    <!-- PEMERIKSAAN -->
                    <!-- ================================================= -->

                    <div class="form-group">
                        <label>
                            Pemeriksaan Laboratorium
                            <span class="required">*</span>
                        </label>

                        <div
                            class="lab-examination-list"
                        >
                            <label
                                v-for="
                                    pemeriksaan in pemeriksaans
                                "
                                :key="pemeriksaan.id"
                                class="examination-item"
                            >
                                <input
                                    type="checkbox"
                                    :value="
                                        pemeriksaan.id
                                    "
                                    v-model="
                                        form.pemeriksaan_ids
                                    "
                                />

                                <div
                                    class="examination-content"
                                >
                                    <strong>
                                        {{
                                            pemeriksaan.nama
                                        }}
                                    </strong>

                                    <small>
                                        {{
                                            pemeriksaan.kode
                                        }}

                                        <span
                                            v-if="
                                                pemeriksaan.kategori
                                            "
                                        >
                                            •
                                            {{
                                                pemeriksaan.kategori
                                            }}
                                        </span>

                                        <span
                                            v-if="
                                                pemeriksaan.satuan
                                            "
                                        >
                                            •
                                            {{
                                                pemeriksaan.satuan
                                            }}
                                        </span>
                                    </small>

                                    <small
                                        v-if="
                                            pemeriksaan.nilai_rujukan
                                        "
                                    >
                                        Nilai rujukan:
                                        {{
                                            pemeriksaan.nilai_rujukan
                                        }}
                                    </small>
                                </div>

                                <div
                                    class="examination-price"
                                >
                                    {{
                                        formatCurrency(
                                            pemeriksaan.harga
                                        )
                                    }}
                                </div>
                            </label>

                            <div
                                v-if="
                                    pemeriksaans.length === 0
                                "
                                class="empty-lab"
                            >
                                Belum ada pemeriksaan
                                laboratorium aktif.
                            </div>
                        </div>

                        <small class="form-help">
                            Dipilih:
                            {{
                                form.pemeriksaan_ids.length
                            }}
                            pemeriksaan
                        </small>
                    </div>

                    <!-- ================================================= -->
                    <!-- CATATAN -->
                    <!-- ================================================= -->

                    <div class="form-group">
                        <label>
                            Catatan
                        </label>

                        <textarea
                            v-model="form.catatan"
                            class="form-control"
                            rows="4"
                            placeholder="Catatan tambahan..."
                        ></textarea>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- FORM FOOTER -->
                <!-- ===================================================== -->

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        :disabled="saving"
                        @click="closeFormModal"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        :disabled="saving"
                        @click="saveLab"
                    >
                        {{
                            saving
                                ? 'Menyimpan...'
                                : editingLab
                                    ? 'Simpan Perubahan'
                                    : 'Simpan Permintaan'
                        }}
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- DETAIL MODAL -->
        <!-- ========================================================= -->

        <div
            v-if="showDetailModal"
            class="modal-overlay"
            @click.self="closeDetailModal"
        >
            <div class="modal modal-xl">
                <div class="modal-header">
                    <div>
                        <h2>
                            Detail Laboratorium
                        </h2>

                        <p>
                            {{ selectedLab?.no_lab }}
                        </p>
                    </div>

                    <button
                        type="button"
                        class="modal-close"
                        @click="closeDetailModal"
                    >
                        ×
                    </button>
                </div>

                <div
                    v-if="selectedLab"
                    class="modal-body"
                >
                    <!-- ================================================= -->
                    <!-- INFORMASI PASIEN -->
                    <!-- ================================================= -->

                    <div class="detail-grid">
                        <div class="detail-item">
                            <span>
                                No. Lab
                            </span>

                            <strong>
                                {{ selectedLab.no_lab }}
                            </strong>
                        </div>

                        <div class="detail-item">
                            <span>
                                Pasien
                            </span>

                            <strong>
                                {{
                                    selectedLab
                                        .pendaftaran
                                        ?.pasien
                                        ?.nama ||
                                    '-'
                                }}
                            </strong>
                        </div>

                        <div class="detail-item">
                            <span>
                                Dokter
                            </span>

                            <strong>
                                {{
                                    selectedLab
                                        .dokter
                                        ?.nama ||
                                    '-'
                                }}
                            </strong>
                        </div>

                        <div class="detail-item">
                            <span>
                                Tanggal
                            </span>

                            <strong>
                                {{
                                    formatDate(
                                        selectedLab
                                            .tanggal_permintaan
                                    )
                                }}
                            </strong>
                        </div>

                        <div class="detail-item">
                            <span>
                                Poli
                            </span>

                            <strong>
                                {{
                                    selectedLab
                                        .pendaftaran
                                        ?.poli
                                        ?.nama ||
                                    '-'
                                }}
                            </strong>
                        </div>

                        <div class="detail-item">
                            <span>
                                Status
                            </span>

                            <strong>
                                <span
                                    class="status-badge"
                                    :class="
                                        statusClass(
                                            selectedLab.status
                                        )
                                    "
                                >
                                    {{
                                        formatStatus(
                                            selectedLab.status
                                        )
                                    }}
                                </span>
                            </strong>
                        </div>
                    </div>

                    <!-- ================================================= -->
                    <!-- STATUS ACTION -->
                    <!-- ================================================= -->

                    <div class="status-section">
                        <h3>
                            Status Pemeriksaan
                        </h3>

                        <div class="status-actions">
                            <button
                                v-for="
                                    status in availableNextStatuses(
                                        selectedLab.status
                                    )
                                "
                                :key="status"
                                type="button"
                                class="btn btn-sm"
                                :class="
                                    statusButtonClass(
                                        status
                                    )
                                "
                                @click="
                                    changeStatus(
                                        selectedLab,
                                        status
                                    )
                                "
                            >
                                {{
                                    formatStatus(
                                        status
                                    )
                                }}
                            </button>
                        </div>
                    </div>

                    <!-- ================================================= -->
                    <!-- HASIL PEMERIKSAAN -->
                    <!-- ================================================= -->

                    <div class="results-section">
                        <div class="section-title">
                            <div>
                                <h3>
                                    Hasil Pemeriksaan
                                </h3>

                                <p>
                                    Isi hasil setiap
                                    pemeriksaan laboratorium.
                                </p>
                            </div>
                        </div>

                        <div class="results-table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>
                                            Pemeriksaan
                                        </th>

                                        <th>
                                            Nilai Rujukan
                                        </th>

                                        <th>
                                            Hasil
                                        </th>

                                        <th>
                                            Flag
                                        </th>

                                        <th>
                                            Catatan
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                        <th>
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr
                                        v-for="
                                            detail in selectedLab.details
                                        "
                                        :key="detail.id"
                                    >
                                        <td>
                                            <strong>
                                                {{
                                                    detail
                                                        .lab_pemeriksaan
                                                        ?.nama ||
                                                    '-'
                                                }}
                                            </strong>

                                            <small>
                                                {{
                                                    detail
                                                        .lab_pemeriksaan
                                                        ?.kode ||
                                                    '-'
                                                }}
                                            </small>
                                        </td>

                                        <td>
                                            {{
                                                detail
                                                    .lab_pemeriksaan
                                                    ?.nilai_rujukan ||
                                                '-'
                                            }}
                                        </td>

                                        <td>
                                            <span
                                                v-if="
                                                    detail.hasil
                                                "
                                            >
                                                {{
                                                    detail
                                                        .hasil
                                                        .hasil
                                                }}
                                            </span>

                                            <span
                                                v-else
                                                class="text-muted"
                                            >
                                                Belum diisi
                                            </span>
                                        </td>

                                        <td>
                                            <span
                                                v-if="
                                                    detail
                                                        .hasil
                                                        ?.flag
                                                "
                                                class="flag-badge"
                                                :class="
                                                    flagClass(
                                                        detail
                                                            .hasil
                                                            .flag
                                                    )
                                                "
                                            >
                                                {{
                                                    detail
                                                        .hasil
                                                        .flag
                                                }}
                                            </span>

                                            <span
                                                v-else
                                            >
                                                -
                                            </span>
                                        </td>

                                        <td>
                                            {{
                                                detail
                                                    .hasil
                                                    ?.catatan ||
                                                '-'
                                            }}
                                        </td>

                                        <td>
                                            <span
                                                v-if="
                                                    detail
                                                        .hasil
                                                        ?.tanggal_verifikasi
                                                "
                                                class="status-badge status-success"
                                            >
                                                Terverifikasi
                                            </span>

                                            <span
                                                v-else-if="
                                                    detail.hasil
                                                "
                                                class="status-badge status-warning"
                                            >
                                                Belum Verifikasi
                                            </span>

                                            <span
                                                v-else
                                                class="status-badge status-gray"
                                            >
                                                Belum Ada Hasil
                                            </span>
                                        </td>

                                        <td>
                                            <div
                                                class="action-buttons"
                                            >
                                                <!-- ISI HASIL -->
                                                <button
                                                    v-if="
                                                        !detail.hasil
                                                    "
                                                    type="button"
                                                    class="btn btn-sm btn-primary"
                                                    @click="
                                                        openHasilModal(
                                                            detail
                                                        )
                                                    "
                                                >
                                                    Isi Hasil
                                                </button>

                                                <!-- EDIT HASIL -->
                                                <button
                                                    v-else-if="
                                                        !detail
                                                            .hasil
                                                            .tanggal_verifikasi
                                                    "
                                                    type="button"
                                                    class="btn btn-sm btn-warning"
                                                    @click="
                                                        openEditHasilModal(
                                                            detail
                                                        )
                                                    "
                                                >
                                                    Edit Hasil
                                                </button>

                                                <!-- VERIFIKASI -->
                                                <button
                                                    v-if="
                                                        detail.hasil &&
                                                        !detail
                                                            .hasil
                                                            .tanggal_verifikasi
                                                    "
                                                    type="button"
                                                    class="btn btn-sm btn-success"
                                                    @click="
                                                        verifyHasil(
                                                            detail
                                                                .hasil
                                                        )
                                                    "
                                                >
                                                    Verifikasi
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr
                                        v-if="
                                            !selectedLab
                                                .details
                                                ?.length
                                        "
                                    >
                                        <td
                                            colspan="7"
                                            class="empty-state"
                                        >
                                            Tidak ada pemeriksaan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ================================================= -->
                    <!-- CATATAN -->
                    <!-- ================================================= -->

                    <div
                        v-if="selectedLab.catatan"
                        class="notes-box"
                    >
                        <strong>
                            Catatan Dokter
                        </strong>

                        <p>
                            {{ selectedLab.catatan }}
                        </p>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- DETAIL FOOTER -->
                <!-- ===================================================== -->

                <div class="modal-footer">
                    <button
                        v-if="
                            selectedLab?.status ===
                            'DIVERIFIKASI'
                        "
                        type="button"
                        class="btn btn-primary"
                        @click="cetakHasilLab"
                    >
                        Cetak Hasil
                    </button>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="closeDetailModal"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODAL HASIL -->
        <!-- ========================================================= -->

        <div
            v-if="showHasilModal"
            class="modal-overlay"
            @click.self="closeHasilModal"
        >
            <div class="modal">
                <div class="modal-header">
                    <div>
                        <h2>
                            {{
                                editingHasil
                                    ? 'Edit Hasil'
                                    : 'Isi Hasil Pemeriksaan'
                            }}
                        </h2>

                        <p>
                            {{
                                selectedDetail
                                    ?.lab_pemeriksaan
                                    ?.nama ||
                                '-'
                            }}
                        </p>
                    </div>

                    <button
                        type="button"
                        class="modal-close"
                        @click="closeHasilModal"
                    >
                        ×
                    </button>
                </div>

                <div class="modal-body">
                    <!-- HASIL -->
                    <div class="form-group">
                        <label>
                            Hasil
                            <span class="required">*</span>
                        </label>

                        <textarea
                            v-model="hasilForm.hasil"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan hasil pemeriksaan..."
                        ></textarea>
                    </div>

                    <!-- FLAG -->
                    <div class="form-group">
                        <label>
                            Flag
                        </label>

                        <select
                            v-model="hasilForm.flag"
                            class="form-control"
                        >
                            <option value="">
                                Tidak ada flag
                            </option>

                            <option value="NORMAL">
                                NORMAL
                            </option>

                            <option value="TINGGI">
                                TINGGI
                            </option>

                            <option value="RENDAH">
                                RENDAH
                            </option>

                            <option value="KRITIS">
                                KRITIS
                            </option>
                        </select>
                    </div>

                    <!-- CATATAN -->
                    <div class="form-group">
                        <label>
                            Catatan
                        </label>

                        <textarea
                            v-model="
                                hasilForm.catatan
                            "
                            class="form-control"
                            rows="3"
                            placeholder="Catatan hasil..."
                        ></textarea>
                    </div>

                    <!-- TANGGAL -->
                    <div class="form-group">
                        <label>
                            Tanggal Pemeriksaan
                        </label>

                        <input
                            v-model="
                                hasilForm.tanggal_pemeriksaan
                            "
                            type="datetime-local"
                            class="form-control"
                        />
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        :disabled="savingHasil"
                        @click="closeHasilModal"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        :disabled="savingHasil"
                        @click="saveHasil"
                    >
                        {{
                            savingHasil
                                ? 'Menyimpan...'
                                : 'Simpan Hasil'
                        }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

const labPermintaans = ref([]);
const pemeriksaans = ref([]);
const pendaftarans = ref([]);
const dokters = ref([]);

const loading = ref(false);
const saving = ref(false);
const savingHasil = ref(false);

const successMessage = ref('');
const errorMessage = ref('');

const showFormModal = ref(false);
const showDetailModal = ref(false);
const showHasilModal = ref(false);

const editingLab = ref(null);
const selectedLab = ref(null);
const selectedDetail = ref(null);
const editingHasil = ref(null);

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

const filters = reactive({
    search: '',
    status: '',
});

/*
|--------------------------------------------------------------------------
| FORM LAB
|--------------------------------------------------------------------------
*/

const form = reactive({
    pendaftaran_id: '',
    dokter_id: '',
    tanggal_permintaan: getToday(),
    catatan: '',
    pemeriksaan_ids: [],
});

/*
|--------------------------------------------------------------------------
| FORM HASIL
|--------------------------------------------------------------------------
*/

const hasilForm = reactive({
    hasil: '',
    flag: '',
    catatan: '',
    tanggal_pemeriksaan: getDateTimeLocal(),
});

/*
|--------------------------------------------------------------------------
| MOUNT
|--------------------------------------------------------------------------
*/

onMounted(async () => {
    await Promise.all([
        loadLabPermintaans(),
        loadPemeriksaans(),
        loadPendaftarans(),
        loadDokters(),
    ]);
});

/*
|--------------------------------------------------------------------------
| LOAD LAB
|--------------------------------------------------------------------------
*/

async function loadLabPermintaans() {
    loading.value = true;
    clearMessages();

    try {
        const response = await axios.get(
            '/api/lab-permintaans',
            {
                params: {
                    search:
                        filters.search || undefined,

                    status:
                        filters.status || undefined,
                },
            }
        );

        labPermintaans.value =
            response.data.data || [];
    } catch (error) {
        handleError(
            error,
            'Gagal mengambil data laboratorium.'
        );
    } finally {
        loading.value = false;
    }
}

/*
|--------------------------------------------------------------------------
| LOAD MASTER PEMERIKSAAN
|--------------------------------------------------------------------------
*/

async function loadPemeriksaans() {
    try {
        const response = await axios.get(
            '/api/lab-pemeriksaans/active'
        );

        pemeriksaans.value =
            response.data.data || [];
    } catch (error) {
        handleError(
            error,
            'Gagal mengambil pemeriksaan laboratorium.'
        );
    }
}

/*
|--------------------------------------------------------------------------
| LOAD PENDAFTARAN
|--------------------------------------------------------------------------
*/

async function loadPendaftarans() {
    try {
        const response = await axios.get(
            '/api/pendaftarans'
        );

        pendaftarans.value =
            response.data.pendaftarans || [];
    } catch (error) {
        handleError(
            error,
            'Gagal mengambil data pendaftaran.'
        );
    }
}

/*
|--------------------------------------------------------------------------
| LOAD DOKTER
|--------------------------------------------------------------------------
*/

async function loadDokters() {
    try {
        const response = await axios.get(
            '/api/dokters'
        );

        dokters.value =
            response.data.dokters || [];
    } catch (error) {
        handleError(
            error,
            'Gagal mengambil data dokter.'
        );
    }
}

/*
|--------------------------------------------------------------------------
| OPEN CREATE
|--------------------------------------------------------------------------
*/

function openCreateModal() {
    editingLab.value = null;

    resetForm();

    clearMessages();

    showFormModal.value = true;
}

/*
|--------------------------------------------------------------------------
| OPEN EDIT
|--------------------------------------------------------------------------
*/

function openEditModal(lab) {
    editingLab.value = lab;

    clearMessages();

    form.pendaftaran_id =
        lab.pendaftaran_id || '';

    form.dokter_id =
        lab.dokter_id || '';

    form.tanggal_permintaan =
        lab.tanggal_permintaan
            ? formatDateInput(
                lab.tanggal_permintaan
            )
            : getToday();

    form.catatan =
        lab.catatan || '';

    form.pemeriksaan_ids =
        (lab.details || []).map(
            detail =>
                detail.lab_pemeriksaan_id
        );

    showFormModal.value = true;
}

/*
|--------------------------------------------------------------------------
| SAVE LAB
|--------------------------------------------------------------------------
*/

async function saveLab() {
    clearMessages();

    if (!form.pendaftaran_id) {
        errorMessage.value =
            'Pasien / pendaftaran wajib dipilih.';

        return;
    }

    if (!form.dokter_id) {
        errorMessage.value =
            'Dokter wajib dipilih.';

        return;
    }

    if (!form.tanggal_permintaan) {
        errorMessage.value =
            'Tanggal permintaan wajib diisi.';

        return;
    }

    if (
        form.pemeriksaan_ids.length === 0
    ) {
        errorMessage.value =
            'Minimal pilih satu pemeriksaan laboratorium.';

        return;
    }

    saving.value = true;

    try {
        const payload = {
            pendaftaran_id:
                Number(
                    form.pendaftaran_id
                ),

            dokter_id:
                Number(
                    form.dokter_id
                ),

            tanggal_permintaan:
                form.tanggal_permintaan,

            catatan:
                form.catatan || null,

            pemeriksaan_ids:
                form.pemeriksaan_ids.map(
                    id => Number(id)
                ),
        };

        if (editingLab.value) {
            await axios.put(
                `/api/lab-permintaans/${editingLab.value.id}`,
                payload
            );

            successMessage.value =
                'Permintaan laboratorium berhasil diperbarui.';
        } else {
            await axios.post(
                '/api/lab-permintaans',
                payload
            );

            successMessage.value =
                'Permintaan laboratorium berhasil dibuat.';
        }

        closeFormModal();

        await loadLabPermintaans();
    } catch (error) {
        handleError(
            error,
            'Gagal menyimpan permintaan laboratorium.'
        );
    } finally {
        saving.value = false;
    }
}

/*
|--------------------------------------------------------------------------
| DETAIL
|--------------------------------------------------------------------------
*/

async function openDetailModal(lab) {
    clearMessages();

    try {
        const response = await axios.get(
            `/api/lab-permintaans/${lab.id}`
        );

        selectedLab.value =
            response.data.data;

        showDetailModal.value = true;
    } catch (error) {
        handleError(
            error,
            'Gagal mengambil detail laboratorium.'
        );
    }
}

function closeDetailModal() {
    showDetailModal.value = false;
    selectedLab.value = null;
}

/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

async function changeStatus(
    lab,
    status
) {
    clearMessages();

    try {
        await axios.put(
            `/api/lab-permintaans/${lab.id}/status`,
            {
                status,
            }
        );

        successMessage.value =
            'Status laboratorium berhasil diperbarui.';

        await openDetailModal(lab);

        await loadLabPermintaans();
    } catch (error) {
        handleError(
            error,
            'Gagal memperbarui status laboratorium.'
        );
    }
}

/*
|--------------------------------------------------------------------------
| AVAILABLE STATUS
|--------------------------------------------------------------------------
*/

function availableNextStatuses(
    currentStatus
) {
    const transitions = {
        MENUNGGU: [
            'SAMPEL_DIAMBIL',
            'BATAL',
        ],

        SAMPEL_DIAMBIL: [
            'DIPROSES',
            'BATAL',
        ],

        DIPROSES: [
            'SELESAI',
            'BATAL',
        ],

        SELESAI: [
            'DIVERIFIKASI',
        ],

        DIVERIFIKASI: [],

        BATAL: [],
    };

    return transitions[
        currentStatus
    ] || [];
}

/*
|--------------------------------------------------------------------------
| HASIL - CREATE
|--------------------------------------------------------------------------
*/

function openHasilModal(detail) {
    selectedDetail.value = detail;
    editingHasil.value = null;

    hasilForm.hasil = '';
    hasilForm.flag = '';
    hasilForm.catatan = '';
    hasilForm.tanggal_pemeriksaan =
        getDateTimeLocal();

    clearMessages();

    showHasilModal.value = true;
}

/*
|--------------------------------------------------------------------------
| HASIL - EDIT
|--------------------------------------------------------------------------
*/

function openEditHasilModal(detail) {
    if (!detail.hasil) {
        return;
    }

    selectedDetail.value = detail;
    editingHasil.value = detail.hasil;

    hasilForm.hasil =
        detail.hasil.hasil || '';

    hasilForm.flag =
        detail.hasil.flag || '';

    hasilForm.catatan =
        detail.hasil.catatan || '';

    hasilForm.tanggal_pemeriksaan =
        detail.hasil.tanggal_pemeriksaan
            ? formatDateTimeInput(
                detail.hasil.tanggal_pemeriksaan
            )
            : getDateTimeLocal();

    clearMessages();

    showHasilModal.value = true;
}

function closeHasilModal() {
    showHasilModal.value = false;
    selectedDetail.value = null;
    editingHasil.value = null;

    hasilForm.hasil = '';
    hasilForm.flag = '';
    hasilForm.catatan = '';
    hasilForm.tanggal_pemeriksaan =
        getDateTimeLocal();
}

/*
|--------------------------------------------------------------------------
| SAVE HASIL
|--------------------------------------------------------------------------
*/

async function saveHasil() {
    clearMessages();

    if (!hasilForm.hasil.trim()) {
        errorMessage.value =
            'Hasil pemeriksaan wajib diisi.';

        return;
    }

    if (!selectedDetail.value) {
        errorMessage.value =
            'Detail pemeriksaan tidak ditemukan.';

        return;
    }

    savingHasil.value = true;

    try {
        const payload = {
            hasil:
                hasilForm.hasil,

            flag:
                hasilForm.flag || null,

            catatan:
                hasilForm.catatan || null,

            tanggal_pemeriksaan:
                hasilForm.tanggal_pemeriksaan ||
                null,
        };

        if (editingHasil.value) {
            await axios.put(
                `/api/lab-hasil/${editingHasil.value.id}`,
                payload
            );

            successMessage.value =
                'Hasil laboratorium berhasil diperbarui.';
        } else {
            await axios.post(
                `/api/lab-permintaan-details/${selectedDetail.value.id}/hasil`,
                payload
            );

            successMessage.value =
                'Hasil laboratorium berhasil disimpan.';
        }

        closeHasilModal();

        if (selectedLab.value) {
            await openDetailModal(
                selectedLab.value
            );
        }

        await loadLabPermintaans();
    } catch (error) {
        handleError(
            error,
            'Gagal menyimpan hasil laboratorium.'
        );
    } finally {
        savingHasil.value = false;
    }
}

/*
|--------------------------------------------------------------------------
| VERIFIKASI HASIL
|--------------------------------------------------------------------------
*/

async function verifyHasil(hasil) {
    const confirmed =
        window.confirm(
            'Apakah Anda yakin ingin memverifikasi hasil ini? Setelah diverifikasi, hasil tidak dapat diubah.'
        );

    if (!confirmed) {
        return;
    }

    clearMessages();

    try {
        await axios.put(
            `/api/lab-hasil/${hasil.id}/verifikasi`,
            {}
        );

        successMessage.value =
            'Hasil laboratorium berhasil diverifikasi.';

        if (selectedLab.value) {
            await openDetailModal(
                selectedLab.value
            );
        }

        await loadLabPermintaans();
    } catch (error) {
        handleError(
            error,
            'Gagal memverifikasi hasil laboratorium.'
        );
    }
}

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

async function deleteLab(lab) {
    const confirmed =
        window.confirm(
            `Hapus permintaan ${lab.no_lab}?`
        );

    if (!confirmed) {
        return;
    }

    clearMessages();

    try {
        await axios.delete(
            `/api/lab-permintaans/${lab.id}`
        );

        successMessage.value =
            'Permintaan laboratorium berhasil dihapus.';

        await loadLabPermintaans();
    } catch (error) {
        handleError(
            error,
            'Gagal menghapus permintaan laboratorium.'
        );
    }
}

/*
|--------------------------------------------------------------------------
| EDIT / DELETE CHECK
|--------------------------------------------------------------------------
*/

function canEditLab(lab) {
    return [
        'MENUNGGU',
    ].includes(lab.status);
}

function canDeleteLab(lab) {
    return [
        'MENUNGGU',
        'BATAL',
    ].includes(lab.status);
}

/*
|--------------------------------------------------------------------------
| FORM RESET
|--------------------------------------------------------------------------
*/

function resetForm() {
    form.pendaftaran_id = '';
    form.dokter_id = '';
    form.tanggal_permintaan =
        getToday();
    form.catatan = '';
    form.pemeriksaan_ids = [];
}

function closeFormModal() {
    showFormModal.value = false;
    editingLab.value = null;

    resetForm();
}

/*
|--------------------------------------------------------------------------
| FILTER RESET
|--------------------------------------------------------------------------
*/

function resetFilter() {
    filters.search = '';
    filters.status = '';

    loadLabPermintaans();
}

/*
|--------------------------------------------------------------------------
| FORMAT DATE
|--------------------------------------------------------------------------
*/

function formatDate(date) {
    if (!date) {
        return '-';
    }

    const value =
        new Date(date);

    if (
        Number.isNaN(
            value.getTime()
        )
    ) {
        return date;
    }

    return value.toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }
    );
}

function formatDateInput(date) {
    if (!date) {
        return '';
    }

    return String(date).substring(
        0,
        10
    );
}

function formatDateTimeInput(date) {
    if (!date) {
        return getDateTimeLocal();
    }

    const value =
        new Date(date);

    if (
        Number.isNaN(
            value.getTime()
        )
    ) {
        return '';
    }

    const year =
        value.getFullYear();

    const month =
        String(
            value.getMonth() + 1
        ).padStart(2, '0');

    const day =
        String(
            value.getDate()
        ).padStart(2, '0');

    const hours =
        String(
            value.getHours()
        ).padStart(2, '0');

    const minutes =
        String(
            value.getMinutes()
        ).padStart(2, '0');

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

/*
|--------------------------------------------------------------------------
| FORMAT CURRENCY
|--------------------------------------------------------------------------
*/

function formatCurrency(value) {
    if (
        value === null ||
        value === undefined
    ) {
        return 'Rp 0';
    }

    return new Intl.NumberFormat(
        'id-ID',
        {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }
    ).format(value);
}

/*
|--------------------------------------------------------------------------
| FORMAT STATUS
|--------------------------------------------------------------------------
*/

function formatStatus(status) {
    const labels = {
        MENUNGGU:
            'Menunggu',

        SAMPEL_DIAMBIL:
            'Sampel Diambil',

        DIPROSES:
            'Diproses',

        SELESAI:
            'Selesai',

        DIVERIFIKASI:
            'Diverifikasi',

        BATAL:
            'Batal',
    };

    return labels[status] || status;
}

/*
|--------------------------------------------------------------------------
| STATUS CLASS
|--------------------------------------------------------------------------
*/

function statusClass(status) {
    const classes = {
        MENUNGGU:
            'status-warning',

        SAMPEL_DIAMBIL:
            'status-info',

        DIPROSES:
            'status-primary',

        SELESAI:
            'status-success',

        DIVERIFIKASI:
            'status-success',

        BATAL:
            'status-danger',
    };

    return classes[status]
        || 'status-gray';
}

/*
|--------------------------------------------------------------------------
| STATUS BUTTON CLASS
|--------------------------------------------------------------------------
*/

function statusButtonClass(status) {
    const classes = {
        SAMPEL_DIAMBIL:
            'btn-info',

        DIPROSES:
            'btn-primary',

        SELESAI:
            'btn-success',

        DIVERIFIKASI:
            'btn-success',

        BATAL:
            'btn-danger',
    };

    return classes[status]
        || 'btn-secondary';
}

/*
|--------------------------------------------------------------------------
| FLAG CLASS
|--------------------------------------------------------------------------
*/

function flagClass(flag) {
    const classes = {
        NORMAL:
            'flag-normal',

        TINGGI:
            'flag-high',

        RENDAH:
            'flag-low',

        KRITIS:
            'flag-critical',
    };

    return classes[flag] || '';
}

/*
|--------------------------------------------------------------------------
| CETAK HASIL LABORATORIUM
|--------------------------------------------------------------------------
*/

function cetakHasilLab() {
    if (!selectedLab.value) {
        return;
    }

    if (
        selectedLab.value.status !==
        'DIVERIFIKASI'
    ) {
        alert(
            'Hasil laboratorium belum diverifikasi.'
        );

        return;
    }

    const url =
        `/api/lab-permintaans/${selectedLab.value.id}/cetak`;

    window.open(
        url,
        '_blank'
    );
}

/*
|--------------------------------------------------------------------------
| DATE HELPERS
|--------------------------------------------------------------------------
*/

function getToday() {
    const date =
        new Date();

    const year =
        date.getFullYear();

    const month =
        String(
            date.getMonth() + 1
        ).padStart(2, '0');

    const day =
        String(
            date.getDate()
        ).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

function getDateTimeLocal() {
    const date =
        new Date();

    const year =
        date.getFullYear();

    const month =
        String(
            date.getMonth() + 1
        ).padStart(2, '0');

    const day =
        String(
            date.getDate()
        ).padStart(2, '0');

    const hours =
        String(
            date.getHours()
        ).padStart(2, '0');

    const minutes =
        String(
            date.getMinutes()
        ).padStart(2, '0');

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

/*
|--------------------------------------------------------------------------
| ERROR HANDLER
|--------------------------------------------------------------------------
*/

function handleError(
    error,
    fallbackMessage
) {
    console.error(error);

    if (
        error.response?.data?.message
    ) {
        errorMessage.value =
            error.response.data.message;

        return;
    }

    if (
        error.response?.data?.errors
    ) {
        const errors =
            error.response.data.errors;

        const firstError =
            Object.values(errors)
                .flat()[0];

        errorMessage.value =
            firstError ||
            fallbackMessage;

        return;
    }

    errorMessage.value =
        fallbackMessage;
}

/*
|--------------------------------------------------------------------------
| CLEAR MESSAGE
|--------------------------------------------------------------------------
*/

function clearMessages() {
    successMessage.value = '';
    errorMessage.value = '';
}
</script>

<style scoped>
/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

.laboratorium-page {
    padding: 24px;
}

/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 24px;
}

.page-header h1 {
    margin: 0 0 6px;
    font-size: 28px;
    font-weight: 700;
}

.page-header p {
    margin: 0;
    color: #6b7280;
}

/*
|--------------------------------------------------------------------------
| CARD
|--------------------------------------------------------------------------
*/

.card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow:
        0 2px 8px
        rgba(0, 0, 0, 0.04);
    margin-bottom: 20px;
}

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

.filter-card {
    padding: 20px;
}

.filter-grid {
    display: grid;
    grid-template-columns:
        minmax(220px, 1fr)
        minmax(180px, 240px)
        auto;
    gap: 16px;
    align-items: end;
}

.filter-actions {
    display: flex;
    gap: 8px;
}

/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 7px;
    font-weight: 600;
    color: #374151;
}

.required {
    color: #dc2626;
}

.form-control {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 14px;
    background: #ffffff;
    transition: 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow:
        0 0 0 3px
        rgba(37, 99, 235, 0.1);
}

.form-help {
    display: block;
    margin-top: 7px;
    color: #6b7280;
}

/*
|--------------------------------------------------------------------------
| BUTTON
|--------------------------------------------------------------------------
*/

.btn {
    border: 0;
    border-radius: 8px;
    padding: 10px 16px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: 0.2s;
}

.btn:hover {
    opacity: 0.9;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-sm {
    padding: 7px 10px;
    font-size: 12px;
}

.btn-primary {
    background: #2563eb;
    color: #ffffff;
}

.btn-secondary {
    background: #6b7280;
    color: #ffffff;
}

.btn-info {
    background: #0891b2;
    color: #ffffff;
}

.btn-warning {
    background: #d97706;
    color: #ffffff;
}

.btn-success {
    background: #16a34a;
    color: #ffffff;
}

.btn-danger {
    background: #dc2626;
    color: #ffffff;
}

/*
|--------------------------------------------------------------------------
| TABLE
|--------------------------------------------------------------------------
*/

.table-wrapper,
.results-table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #f9fafb;
    color: #374151;
    font-size: 13px;
    font-weight: 700;
    text-align: left;
    padding: 13px 14px;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
}

.data-table td {
    padding: 14px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
    font-size: 14px;
}

.data-table tbody tr:hover {
    background: #fafafa;
}

.data-table small {
    display: block;
    color: #6b7280;
    margin-top: 3px;
}

.patient-name {
    font-weight: 600;
}

.action-buttons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

.status-badge,
.flag-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 5px 9px;
    font-size: 11px;
    font-weight: 700;
}

.status-warning {
    background: #fef3c7;
    color: #92400e;
}

.status-info {
    background: #cffafe;
    color: #155e75;
}

.status-primary {
    background: #dbeafe;
    color: #1e40af;
}

.status-success {
    background: #dcfce7;
    color: #166534;
}

.status-danger {
    background: #fee2e2;
    color: #991b1b;
}

.status-gray {
    background: #f3f4f6;
    color: #4b5563;
}

/*
|--------------------------------------------------------------------------
| FLAGS
|--------------------------------------------------------------------------
*/

.flag-normal {
    background: #dcfce7;
    color: #166534;
}

.flag-high {
    background: #fef3c7;
    color: #92400e;
}

.flag-low {
    background: #dbeafe;
    color: #1e40af;
}

.flag-critical {
    background: #fee2e2;
    color: #991b1b;
}

/*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/

.modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0, 0, 0, 0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal {
    width: 100%;
    max-width: 650px;
    max-height: 90vh;
    overflow-y: auto;
    background: #ffffff;
    border-radius: 14px;
    box-shadow:
        0 20px 50px
        rgba(0, 0, 0, 0.2);
}

.modal-lg {
    max-width: 850px;
}

.modal-xl {
    max-width: 1250px;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.modal-header h2 {
    margin: 0 0 5px;
    font-size: 20px;
}

.modal-header p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
}

.modal-close {
    border: 0;
    background: transparent;
    font-size: 28px;
    cursor: pointer;
    color: #6b7280;
    line-height: 1;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/*
|--------------------------------------------------------------------------
| LAB EXAMINATION
|--------------------------------------------------------------------------
*/

.lab-examination-list {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    max-height: 350px;
    overflow-y: auto;
}

.examination-item {
    display: flex !important;
    align-items: center;
    gap: 12px;
    padding: 13px;
    margin: 0 !important;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
}

.examination-item:last-child {
    border-bottom: 0;
}

.examination-item:hover {
    background: #f9fafb;
}

.examination-item input {
    width: 17px;
    height: 17px;
    flex-shrink: 0;
}

.examination-content {
    flex: 1;
}

.examination-content strong {
    display: block;
}

.examination-content small {
    display: block;
    color: #6b7280;
    margin-top: 3px;
}

.examination-price {
    font-weight: 700;
    white-space: nowrap;
}

.empty-lab {
    padding: 25px;
    text-align: center;
    color: #6b7280;
}

/*
|--------------------------------------------------------------------------
| DETAIL
|--------------------------------------------------------------------------
*/

.detail-grid {
    display: grid;
    grid-template-columns:
        repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 25px;
}

.detail-item {
    padding: 14px;
    background: #f9fafb;
    border-radius: 9px;
}

.detail-item span {
    display: block;
    color: #6b7280;
    font-size: 12px;
    margin-bottom: 5px;
}

.detail-item strong {
    display: block;
}

/*
|--------------------------------------------------------------------------
| STATUS SECTION
|--------------------------------------------------------------------------
*/

.status-section {
    margin-bottom: 25px;
}

.status-section h3,
.results-section h3 {
    margin: 0 0 6px;
}

.status-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 12px;
}

/*
|--------------------------------------------------------------------------
| SECTION
|--------------------------------------------------------------------------
*/

.results-section {
    margin-top: 20px;
}

.section-title p {
    margin: 0 0 15px;
    color: #6b7280;
    font-size: 13px;
}

/*
|--------------------------------------------------------------------------
| NOTES
|--------------------------------------------------------------------------
*/

.notes-box {
    margin-top: 20px;
    padding: 15px;
    border-radius: 9px;
    background: #f9fafb;
}

.notes-box p {
    margin: 8px 0 0;
    white-space: pre-wrap;
}

/*
|--------------------------------------------------------------------------
| ALERT
|--------------------------------------------------------------------------
*/

.alert {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: 14px;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
}

/*
|--------------------------------------------------------------------------
| LOADING
|--------------------------------------------------------------------------
*/

.loading-box {
    padding: 40px;
    text-align: center;
    color: #6b7280;
}

.empty-state {
    text-align: center;
    padding: 40px !important;
    color: #6b7280;
}

.text-muted {
    color: #9ca3af;
}

/*
|--------------------------------------------------------------------------
| RESPONSIVE
|--------------------------------------------------------------------------
*/

@media (max-width: 900px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }

    .detail-grid {
        grid-template-columns:
            1fr 1fr;
    }

    .page-header {
        align-items: flex-start;
        flex-direction: column;
    }
}

@media (max-width: 600px) {
    .laboratorium-page {
        padding: 15px;
    }

    .detail-grid {
        grid-template-columns: 1fr;
    }

    .modal-overlay {
        padding: 10px;
    }

    .modal {
        max-height: 95vh;
    }
}
</style>
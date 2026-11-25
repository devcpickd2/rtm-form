@extends('layouts.app')
@php
    $user = auth()->user();

    if (!$user) {
        header('Location: ' . route('login'));
        exit();
    }

    $type_user = $user->type_user;
@endphp
@section('content')
    <div class="container-fluid py-0">
        {{-- Alert sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="bi bi-list-check"></i> Data Pemeriksaan Kekuatan Magnet Trap</h3>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('kekuatan_magnet.create') }}" class="btn btn-success m-1">
                            <i class="bi bi-plus-circle"></i> Tambah
                        </a>

                        @if (in_array($type_user, [0, 2, 8]))
                            <a href="{{ route('kekuatan_magnet.audit') }}" class="btn btn-audit m-1">
                                <i class="bi bi-clipboard-check"></i> Audit Data
                            </a>

                            <a href="{{ route('kekuatan_magnet.recyclebin') }}" class="btn btn-secondary">
                                <i class="bi bi-trash"></i> Recycle Bin
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Filter tanggal (server-side, agar akurat untuk semua halaman pagination) --}}
                <form id="filterForm" method="GET" action="{{ route('kekuatan_magnet.index') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <div class="input-group" style="max-width: 220px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                        <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                            value="{{ request('date') }}" placeholder="Tanggal Produksi">
                    </div>

                    @if (request('date'))
                        <a href="{{ route('kekuatan_magnet.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Nama Alat</th>
                                <th>Kondisi Magnet (Visual)</th>
                                <th>Parameter Setting</th>
                                <th>Nilai Pengukuran (Gauss)</th>
                                <th>Keterangan</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>SPV</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @php $no = ($data->currentPage() - 1) * $data->perPage() + 1; @endphp
                            @forelse ($data as $dep)
                                <tr>
                                    <td class="text-center align-middle">{{ $no++ }}</td>
                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift:
                                        {{ $dep->shift }}</td>
                                    <td class="text-center align-middle">{{ $dep->nama_alat }}</td>
                                    <td class="text-center align-middle">{{ $dep->kondisi }}</td>
                                    <td class="text-center align-middle">{{ $dep->parameter }}</td>
                                    <td class="text-center align-middle">{{ $dep->gauss }}</td>
                                    <td class="text-center align-middle">{{ $dep->keterangan ?: '-' }}</td>
                                    <td class="text-center align-middle">{{ $dep->username }}</td>
                                    <td class="text-center align-middle">{{ $dep->nama_produksi }}</td>
                                    <td class="text-center align-middle">
                                        @if ($dep->status_spv == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_spv == 1)
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                class="text-danger fw-bold text-decoration-none"
                                                style="cursor: pointer;">Revision</a>

                                            <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1"
                                                aria-labelledby="revisionModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title"
                                                                id="revisionModalLabel{{ $dep->uuid }}">Detail Revisi
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <ul class="list-unstyled mb-0">
                                                                <li><strong>Status:</strong> Revision</li>
                                                                <li><strong>Catatan:</strong>
                                                                    {{ $dep->catatan_spv ?? '-' }}</li>
                                                            </ul>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm"
                                                                data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('kekuatan_magnet.edit', $dep->uuid) }}"
                                            class="btn btn-warning btn-sm me-1">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>
                                        @if (in_array($type_user, [0, 2, 8]))
                                            <div class="d-inline-flex gap-1">
                                                <button type="button"
                                                    class="btn btn-primary btn-sm fw-bold shadow-sm js-open-verify"
                                                    data-url="{{ route('kekuatan_magnet.verification.update', $dep->uuid) }}"
                                                    data-status="{{ $dep->status_spv }}"
                                                    data-catatan="{{ $dep->catatan_spv }}" title="Verifikasi data ini">
                                                    <i class="bi bi-shield-check me-1"></i> Verifikasi
                                                </button>

                                                <form action="{{ route('kekuatan_magnet.destroy', $dep->uuid) }}"
                                                    method="POST" class="d-inline js-delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        title="Hapus data ini">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center align-middle">Belum ada data kekuatan magnet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
                @if (in_array($type_user, [0, 2, 8]))
                    <form method="GET" action="{{ route('kekuatan_magnet.exportPdf') }}">

                        <div class="d-flex align-items-center flex-wrap">

                            <label for="year" class="fw-semibold mb-0 export-label">
                                <i class="bi bi-calendar3 me-1 text-danger"></i>
                                Periode Tahun
                            </label>

                            <select id="year" name="year" class="year-select" required>
                                <option value="">Pilih Tahun</option>

                                @for ($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>

                            <button type="submit" class="btn btn-danger btn-sm px-3 export-btn">
                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                Export PDF
                            </button>

                        </div>

                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= SHARED MODAL: VERIFIKASI (satu modal untuk semua baris) ================= --}}
    <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <form id="verifyForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white kmt-verify-modal">
                    <div class="modal-header border-bottom border-light-subtle p-4">
                        <h5 class="modal-title fw-bolder fs-4 text-uppercase" id="verifyModalLabel">
                            <i class="bi bi-gear-fill me-2"></i> Verification
                        </h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" id="closeVerifyModal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 p-md-5">
                        <p class="text-light-emphasis mb-4 fs-6">
                            Pastikan data yang akan diverifikasi sudah diperiksa dengan teliti terlebih dahulu.
                        </p>

                        <div class="mb-4">
                            <label for="status_spv" class="form-label fw-bold mb-2 text-center d-block kmt-label">
                                Pilih Status Verifikasi
                            </label>
                            <select name="status_spv" id="status_spv"
                                class="form-select form-select-lg fw-bold text-center mx-auto kmt-select" required>
                                <option value="1" style="color:#198754;">✅ Verified (Disetujui)</option>
                                <option value="2" style="color:#dc3545;">❌ Revision (Perlu Perbaikan)</option>
                            </select>
                        </div>

                        <div>
                            <label for="catatan_spv" class="form-label fw-bold text-light mb-2">
                                Catatan Tambahan (Opsional)
                            </label>
                            <textarea name="catatan_spv" id="catatan_spv" rows="4"
                                class="form-control text-dark border-0 shadow-none kmt-textarea"
                                placeholder="Masukkan catatan, misalnya alasan revisi..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-end p-4 border-top kmt-verify-footer">
                        <button type="button" class="btn btn-outline-light fw-bold rounded-pill px-4 me-2"
                            id="cancelVerifyModal">Batal</button>
                        <button type="submit" class="btn fw-bolder rounded-pill px-5 kmt-submit-btn">
                            <i class="bi bi-save-fill me-1"></i> Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= SHARED MODAL: DETAIL REVISI (satu modal untuk semua baris) ================= --}}
    <div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="revisionModalLabel"><i class="bi bi-exclamation-circle-fill me-1"></i>
                        Detail Revisi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled mb-0">
                        <li><strong>Status:</strong> Revision</li>
                        <li class="mt-2"><strong>Catatan:</strong> <span id="revisionCatatanText">-</span></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-hide alert setelah 3 detik
            document.querySelectorAll('.alert').forEach(alert => {
                setTimeout(() => {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                }, 3000);
            });

            // Filter tanggal: submit form ke server (bukan filter DOM lokal)
            // supaya hasil tetap akurat walau data terpaginasi ke banyak halaman.
            const filterForm = document.getElementById('filterForm');
            const dateInput = document.getElementById('filter_date');
            dateInput?.addEventListener('change', () => filterForm.submit());

            // ---- Modal Verifikasi (shared, di-populate via delegasi event) ----
            const verifyModalEl = document.getElementById('verifyModal');
            const verifyModal = new bootstrap.Modal(verifyModalEl);

            const verifyForm = document.getElementById('verifyForm');
            const statusSelect = document.getElementById('status_spv');
            const catatanInput = document.getElementById('catatan_spv');

            document.getElementById('closeVerifyModal').addEventListener('click', () => {
                verifyModal.hide();
            });

            document.getElementById('cancelVerifyModal').addEventListener('click', () => {
                verifyModal.hide();
            });

            // ---- Modal Detail Revisi (shared) ----
            const revisionModalEl = document.getElementById('revisionModal');
            const revisionModal = new bootstrap.Modal(revisionModalEl);
            const revisionCatatanText = document.getElementById('revisionCatatanText');

            // Delegasi klik: satu listener untuk seluruh tabel, tidak perlu bind per baris
            document.getElementById('tableBody').addEventListener('click', (e) => {
                const verifyBtn = e.target.closest('.js-open-verify');
                if (verifyBtn) {
                    verifyForm.action = verifyBtn.dataset.url;
                    statusSelect.value = verifyBtn.dataset.status || '1';
                    catatanInput.value = verifyBtn.dataset.catatan || '';
                    verifyModal.show();
                    return;
                }

                const revisionBtn = e.target.closest('.js-open-revision');
                if (revisionBtn) {
                    revisionCatatanText.textContent = revisionBtn.dataset.catatan || '-';
                    revisionModal.show();
                    return;
                }
            });

            // Konfirmasi hapus (delegasi, tidak pakai inline onclick)
            document.getElementById('tableBody').addEventListener('submit', (e) => {
                if (e.target.classList.contains('js-delete-form')) {
                    if (!confirm('Yakin ingin menghapus?')) {
                        e.preventDefault();
                    }
                }
            });

            // Loading state tombol export PDF, mencegah double submit
            const exportForm = document.querySelector('.js-export-form');
            exportForm?.addEventListener('submit', () => {
                const btn = exportForm.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyiapkan...';
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-hide alert setelah 3 detik
            const alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(() => {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                }, 3000);
            }

            // Filter tanggal: submit form ke server (bukan filter DOM lokal)
            // supaya hasil tetap akurat walau data terpaginasi ke banyak halaman.
            const form = document.getElementById('filterForm');
            const dateInput = document.getElementById('filter_date');

            dateInput.addEventListener('change', () => form.submit());
        });
    </script>

    <style>
        .table td,
        .table th {
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .text-danger {
            font-weight: bold;
        }

        .text-muted.fst-italic {
            color: #6c757d !important;
            font-style: italic !important;
        }

        .container {
            padding-left: 2px !important;
            padding-right: 2px !important;
        }

        /* Modal verifikasi */
        .kmt-verify-modal {
            background: linear-gradient(145deg, #7a1f12, #9E3419);
        }

        .kmt-verify-footer {
            background-color: #9E3419;
            border-color: #00ffc4 !important;
        }

        .kmt-label {
            color: #FFE5DE;
            font-size: 0.95rem;
        }

        .kmt-select {
            background: linear-gradient(135deg, #fff1f0, #ffe5de);
            border: 2px solid #dc3545;
            border-radius: 12px;
            color: #dc3545;
            height: 55px;
            font-size: 1.1rem;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 85%;
            margin: 0 auto;
            display: block;
            transition: all .3s ease;
        }

        .kmt-textarea {
            background-color: #FFE5DE;
            min-height: 120px;
        }

        .kmt-submit-btn {
            background-color: #E39581;
            color: #2c3e50;
        }

        .kmt-submit-btn:hover {
            background-color: #eaa998;
            color: #2c3e50;
        }

        .text-danger {
            font-weight: bold;
        }

        .text-muted.fst-italic {
            color: #6c757d !important;
            font-style: italic !important;
        }

        .container {
            padding-left: 2px !important;
            padding-right: 2px !important;
        }

        .export-label {
            margin-right: 5px !important;
        }

        .year-select {
            width: 150px;
            height: 36px;
            padding: 0 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #495057;
            background-color: #fff;
            cursor: pointer;
            outline: none;
            margin-right: 5px !important;
        }

        .export-btn {
            margin-left: 0 !important;
        }

        .year-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.15);
        }

        .btn-audit {
            background-color: #7c3aed;
            color: #fff;
            border-color: #7c3aed;
        }

        .btn-audit:hover {
            background-color: #6d28d9;
            color: #fff;
            border-color: #6d28d9;
        }
    </style>
@endsection

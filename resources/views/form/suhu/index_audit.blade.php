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
                {{-- HEADER --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="mb-0 fw-bold">
                            <i class="bi bi-list-check me-1"></i>
                            Data Pemeriksaan Suhu Ruang
                            <span class="badge audit-badge">MODE AUDIT</span>
                        </h3>
                    </div>
                </div>
                {{-- BREADCRUMB --}}
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('suhu.index') }}">
                        <i class="bi bi-arrow-left me-1"></i>
                        Data Pemeriksaan Suhu
                    </a>

                    <span class="mx-2">/</span>

                    <span class="fw-bold">Data Audit</span>
                </div>

                {{-- NOTE HALAMAN AUDIT --}}
                <div class="alert audit-info d-flex align-items-start mb-4" role="alert">
                    <i class="bi bi-shield-check me-3 fs-4"></i>

                    <div>
                        <strong class="d-block mb-1">Halaman Data Audit</strong>
                        <div class="small">
                            Anda sedang melihat data pemeriksaan suhu yang tersimpan pada halaman Audit.
                            Data di halaman ini merupakan salinan data untuk kebutuhan pemeriksaan dan audit,
                            sehingga perubahan pada data Audit tidak memengaruhi data operasional asli.
                        </div>
                    </div>
                </div>

                {{-- Filter, Reset, dan Multiple Verification --}}
                <form id="filterForm" method="GET" action="{{ route('suhu.index') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    {{-- Filter Tanggal --}}
                    <div class="input-group" style="max-width: 220px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>

                        <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                            value="{{ request('date') }}">
                    </div>

                    {{-- Tombol Filter --}}
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>
                        Filter
                    </button>

                    {{-- Tombol Reset --}}
                    @if (request('date'))
                        <a href="{{ route('suhu.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>
                    @endif

                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const searchInput = document.getElementById('search');
                        const dateInput = document.getElementById('filter_date');
                        const rows = document.querySelectorAll('#tableBody tr');

                        const filterTable = () => {
                            let search = searchInput.value.toLowerCase();
                            let date = dateInput.value;

                            rows.forEach(row => {
                                let rowText = row.innerText.toLowerCase();
                                let dateText = row.cells[1]?.innerText || "";

                                let matchSearch = !search || rowText.includes(search);
                                let matchDate = !date || dateText.includes(date);

                                row.style.display = (matchSearch && matchDate) ? "" : "none";
                            });
                        };

                        searchInput.addEventListener('input', filterTable);
                        dateInput.addEventListener('change', filterTable);
                    });
                </script>

                {{-- ================= MODAL: VERIFIKASI RENTANG TANGGAL (BULK) ================= --}}
                <div class="modal fade" id="verifikasiTanggalModal" tabindex="-1"
                    aria-labelledby="verifikasiTanggalModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header border-0 px-4 py-3 bg-danger-gradient">
                                <div>
                                    <h5 class="modal-title text-white fw-bold mb-1" id="verifikasiTanggalModalLabel">
                                        <i class="bi bi-calendar-check me-2"></i>
                                        Multiple Verification
                                    </h5>
                                    <small class="text-white-50">Verifikasi seluruh data suhu sekaligus</small>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form method="POST" action="{{ route('suhu.verification.bulk') }}" id="bulkVerificationForm">
                                @csrf
                                @method('PUT')
                                <div class="modal-body px-4 py-4">
                                    <div class="alert alert-light border rounded-3 mb-4">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-info-circle-fill text-primary me-2 mt-1"></i>
                                            <div class="small">
                                                Pilih rentang tanggal dan status verifikasi.
                                                Semua data yang berada dalam rentang tersebut akan diproses sekaligus.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label for="bulk_date_start" class="form-label fw-semibold">
                                                <i class="bi bi-calendar-event me-1 text-danger"></i> Tanggal Mulai
                                            </label>
                                            <input type="date" name="date_start" id="bulk_date_start"
                                                class="form-control form-control-lg" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="bulk_date_end" class="form-label fw-semibold">
                                                <i class="bi bi-calendar-event me-1 text-danger"></i> Tanggal Sampai
                                            </label>
                                            <input type="date" name="date_end" id="bulk_date_end"
                                                class="form-control form-control-lg" required>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label for="bulk_status_spv" class="form-label fw-semibold mb-2">
                                            <i class="bi bi-shield-check me-1 text-danger"></i> Status Verifikasi
                                        </label>
                                        <select name="status_spv" id="bulk_status_spv" class="form-select glass-select"
                                            required>
                                            <option value="" selected disabled>-- Pilih Status Verifikasi --</option>
                                            <option value="1">✓ Verified — Disetujui</option>
                                            <option value="2">✕ Revision — Perlu Perbaikan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 bg-light px-4 py-3">
                                    <button type="button" class="btn btn-outline-secondary px-4"
                                        data-bs-dismiss="modal">
                                        <i class="bi bi-x-lg me-1"></i> Batal
                                    </button>
                                    <button type="submit" class="btn btn-danger fw-semibold px-4"
                                        id="btnBulkVerification">
                                        <i class="bi bi-check-circle me-1"></i> Verifikasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Tambahkan table-responsive agar tabel tidak keluar border --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Pukul</th>
                                <th>Chillroom<br><small>(0–4 °C)</small></th>
                                <th>Cold Stor.<br>1<br><small>(-22 – (-18) °C)</small></th>
                                <th>Cold Stor.<br>2<br><small>(-22 – (-18) °C)</small></th>
                                <th>Anteroom<br>RM<br><small>(8–10 °C)</small></th>
                                <th>Seasoning<br><small>(22–30 °C / ≤75% RH)</small></th>
                                <th>Prep.<br>Room<br><small>(9–15 °C)</small></th>
                                <th>Cooking<br><small>(20–30 °C)</small></th>
                                <th>Filling<br><small>(9–15 °C)</small></th>
                                <th>Rice<br><small>(20–30 °C)</small></th>
                                <th>Noodle<br><small>(20–30 °C)</small></th>
                                <th>Topping<br><small>(9–15 °C)</small></th>
                                <th>Packing<br><small>(9–15 °C)</small></th>
                                <th>DS<br><small>(20–30 °C / ≤75% RH)</small></th>
                                <th>Cold Stor.<br>FG<br><small>(-20 – (-18) °C)</small></th>
                                <th>Anteroom<br>FG<br><small>(0–10 °C)</small></th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>SPV</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @php
                                $no = ($data->currentPage() - 1) * $data->perPage() + 1;

                            @endphp
                            @forelse ($data as $dep)
                                <tr>
                                    <td class="text-center align-middle">{{ $no++ }}</td>
                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift:
                                        {{ $dep->shift }}</td>
                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}</td>

                                    {{-- Chillroom 0-4 --}}
                                    <td class="{{ cekRange($dep->chillroom, 0, 4) }} text-center align-middle">
                                        {{ $dep->chillroom ?? 'Belum dicek' }}</td>
                                    {{-- CS1 -22 s/d -18 --}}
                                    <td class="{{ cekRange($dep->cs_1, -22, -18) }} text-center align-middle">
                                        {{ $dep->cs_1 ?? 'Belum dicek' }}</td>
                                    {{-- CS2 -22 s/d -18 --}}
                                    <td class="{{ cekRange($dep->cs_2, -22, -18) }} text-center align-middle">
                                        {{ $dep->cs_2 ?? 'Belum dicek' }}</td>
                                    {{-- Anteroom RM 8-10 --}}
                                    <td class="{{ cekRange($dep->anteroom_rm, 8, 10) }} text-center align-middle">
                                        {{ $dep->anteroom_rm ?? 'Belum dicek' }}</td>
                                    {{-- Seasoning Suhu 22-30 | RH <=75 --}}
                                    <td class="text-center align-middle">
                                        <span
                                            class="{{ cekRange($dep->seasoning_suhu, 22, 30) }} text-center align-middle">{{ $dep->seasoning_suhu ?? 'Belum dicek' }}</span>
                                        |
                                        <span
                                            class="{{ cekRange($dep->seasoning_rh, 0, 75) }} text-center align-middle">{{ $dep->seasoning_rh ?? 'Belum dicek' }}</span>
                                    </td>
                                    {{-- Prep Room 9-15 --}}
                                    <td class="{{ cekRange($dep->prep_room, 9, 15) }} text-center align-middle">
                                        {{ $dep->prep_room ?? 'Belum dicek' }}</td>
                                    {{-- Cooking 20-30 --}}
                                    <td class="{{ cekRange($dep->cooking, 20, 30) }} text-center align-middle">
                                        {{ $dep->cooking ?? 'Belum dicek' }}</td>
                                    {{-- Filling 9-15 --}}
                                    <td class="{{ cekRange($dep->filling, 9, 15) }} text-center align-middle">
                                        {{ $dep->filling ?? 'Belum dicek' }}</td>
                                    {{-- Rice 20-30 --}}
                                    <td class="{{ cekRange($dep->rice, 20, 30) }} text-center align-middle">
                                        {{ $dep->rice ?? 'Belum dicek' }}</td>
                                    {{-- Noodle 20-30 --}}
                                    <td class="{{ cekRange($dep->noodle, 20, 30) }} text-center align-middle">
                                        {{ $dep->noodle ?? 'Belum dicek' }}</td>
                                    {{-- Topping 9-15 --}}
                                    <td class="{{ cekRange($dep->topping, 9, 15) }} text-center align-middle">
                                        {{ $dep->topping ?? 'Belum dicek' }}</td>
                                    {{-- Packing 9-15 --}}
                                    <td class="{{ cekRange($dep->packing, 9, 15) }} text-center align-middle">
                                        {{ $dep->packing ?? 'Belum dicek' }}</td>
                                    {{-- DS Suhu 20-30 | RH <=75 --}}
                                    <td class="text-center align-middle">
                                        <span
                                            class="{{ cekRange($dep->ds_suhu, 20, 30) }} text-center align-middle">{{ $dep->ds_suhu ?? 'Belum dicek' }}</span>
                                        |
                                        <span
                                            class="{{ cekRange($dep->ds_rh, 0, 75) }} text-center align-middle">{{ $dep->ds_rh ?? 'Belum dicek' }}</span>
                                    </td>
                                    {{-- CS FG -20 s/d -18 --}}
                                    <td class="{{ cekRange($dep->cs_fg, -20, -18) }} text-center align-middle">
                                        {{ $dep->cs_fg ?? 'Belum dicek' }}</td>
                                    {{-- Anteroom FG 0-10 --}}
                                    <td class="{{ cekRange($dep->anteroom_fg, 0, 10) }} text-center align-middle">
                                        {{ $dep->anteroom_fg ?? 'Belum dicek' }}</td>
                                    <td class="text-center align-middle">{{ $dep->username }}</td>
                                    <td class="text-center align-middle">{{ $dep->nama_produksi }}</td>
                                    <!-- <td class="text-center align-middle">
                                                                                                            @if ($dep->status_produksi == 0)
    <span class="fw-bold text-secondary">Created</span>
@elseif ($dep->status_produksi == 1)
    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#checkedModal{{ $dep->uuid }}"
                                                                                                                class="fw-bold text-success text-decoration-none" style="cursor: pointer; font-weight: bold;">Checked</a>
                                                                                                                <div class="modal fade" id="checkedModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="checkedModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                                                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                                                                        <div class="modal-content">
                                                                                                                            <div class="modal-header bg-success text-white">
                                                                                                                                <h5 class="modal-title" id="checkedModalLabel{{ $dep->uuid }}">Detail Checked</h5>
                                                                                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                                                            </div>
                                                                                                                            <div class="modal-body">
                                                                                                                                <ul class="list-unstyled mb-0">
                                                                                                                                    <li><strong>Status:</strong> Checked</li>
                                                                                                                                    <li><strong>Nama Produksi:</strong> {{ $dep->nama_produksi ?? '-' }}</li>
                                                                                                                                </ul>
                                                                                                                            </div>
                                                                                                                            <div class="modal-footer">
                                                                                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
@elseif ($dep->status_produksi == 2)
    <span class="fw-bold text-danger">Recheck</span>
    @endif
                                                                                                            </td> -->

                                    <td class="text-center align-middle">
                                        @if ($dep->status_spv == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_spv == 1)
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <!-- Link buka modal -->
                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                class="text-danger fw-bold text-decoration-none"
                                                style="cursor: pointer;">Revision</a>

                                            <!-- Modal -->
                                            <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1"
                                                aria-labelledby="revisionModalLabel{{ $dep->uuid }}"
                                                aria-hidden="true">
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

                                    <td class="text-center align-middle">
                                        <a href="{{ route('suhu.edit', $dep->uuid) }}"
                                            class="btn btn-warning btn-sm me-1">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>
                                        @if (in_array($type_user, [0, 2, 8]))
                                            <form action="{{ route('suhu.destroy', $dep->uuid) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="19" class="text-center align-middle">Belum ada data suhu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

                @if (in_array($type_user, [0, 2, 8]))
                    <form action="{{ route('suhu.export') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label for="date" class="col-form-label fw-semibold">Pilih Tanggal</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" id="date" name="date" class="form-control form-control-sm"
                                value="{{ request('date') }}" required>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="submit" formaction="{{ route('suhu.exportPdfAudit') }}"
                                class="btn btn-danger btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> Export PDF
                            </button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>

    {{-- Auto-hide alert setelah 3 detik --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertElement = document.querySelector(
                '.alert-success.alert-dismissible, .alert-danger.alert-dismissible'
            );

            if (alertElement) {
                setTimeout(function() {
                    const alertInstance = bootstrap.Alert.getOrCreateInstance(alertElement);
                    alertInstance.close();
                }, 3000);
            }
        });
    </script>

    {{-- CSS tambahan agar tabel lebih rapi --}}
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

        /* Header tabel */
        .table thead,
        .table thead th {
            background-color: #cfe2ff !important;
            color: #000 !important;
            border-color: #dee2e6 !important;
        }

        /* Baris tabel stripe */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa !important;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff !important;
        }

        /* Hover baris */
        .table tbody tr:hover {
            background-color: #e9ecef !important;
            color: #212529 !important;
        }

        /* Border tabel */
        .table-bordered th,
        .table-bordered td {
            border-color: #dee2e6 !important;
        }

        /* Tombol aksi tetap jelas */
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #b02a37;
            border-color: #a52834;
        }

        /* Gradient header modal bulk verification */
        .bg-danger-gradient {
            background: linear-gradient(135deg, #dc3545, #a71d2a);
        }

        /* Modal bulk verification */
        #verifikasiTanggalModal .modal-content {
            animation: modalVerificationShow 0.2s ease-out;
        }

        #verifikasiTanggalModal .form-control,
        #verifikasiTanggalModal .form-select {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        #verifikasiTanggalModal .form-control:focus,
        #verifikasiTanggalModal .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.12);
        }

        #verifikasiTanggalModal .form-label {
            font-size: 0.9rem;
            color: #343a40;
        }

        #verifikasiTanggalModal .alert {
            background: #f8f9fa;
        }

        #verifikasiTanggalModal .btn {
            border-radius: 9px;
        }

        @keyframes modalVerificationShow {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .header-btn {
            height: 38px;
            min-width: 140px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 14px;
        }


        /* =========================================
                       AUDIT BREADCRUMB
                    ========================================= */
        .audit-breadcrumb {
            background: #dc3545;
            border: 1px solid #dc3545;
            border-radius: 8px;
            padding: 11px 15px;
            font-size: 0.88rem;
            color: #ffffff;
            box-shadow: 0 2px 5px rgba(220, 53, 69, 0.15);
        }

        .audit-breadcrumb a {
            color: #ffffff;
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .audit-breadcrumb a:hover {
            color: #ffe3e6;
            text-decoration: underline;
        }

        .audit-breadcrumb span {
            color: #ffffff !important;
        }

        .audit-breadcrumb span:last-child {
            font-weight: 700;
        }

        /* =========================================
                       AUDIT NOTE
                    ========================================= */
        .audit-info {
            background: #f5f0ff;
            border: 1px solid #e9d5ff;
            border-left: 4px solid #7c3aed;
            border-radius: 8px;
            color: #4c1d95;
            padding: 14px 16px;
        }

        .audit-info i {
            color: #7c3aed;
        }

        .audit-info strong {
            color: #6b21a8;
            font-size: 0.95rem;
        }

        /* =========================================
                       AUDIT BADGE
                    ========================================= */
        .audit-badge {
            background: #7c3aed;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 6px 10px;
            border-radius: 5px;
            vertical-align: middle;
        }

        /* =========================================
                       HEADER BUTTON
                    ========================================= */
        .header-btn {
            height: 38px;
            min-width: 140px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 14px;
        }
    </style>
@endsection

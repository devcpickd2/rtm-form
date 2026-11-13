@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-list-check"></i> Data Pemeriksaan Kekuatan Magnet Trap</h3>
                <a href="{{ route('kekuatan_magnet.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
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

            @if(request('date'))
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
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}</td>
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
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revisionModal{{ $dep->uuid }}"
                             class="text-danger fw-bold text-decoration-none" style="cursor: pointer;">Revision</a>

                             <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="revisionModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="revisionModalLabel{{ $dep->uuid }}">Detail Revisi</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-unstyled mb-0">
                                                <li><strong>Status:</strong> Revision</li>
                                                <li><strong>Catatan:</strong> {{ $dep->catatan_spv ?? '-' }}</li>
                                            </ul>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('kekuatan_magnet.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                                <i class="bi bi-pencil"></i> Update
                            </a>
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
    </div>
</div>
</div>

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
    .table td, .table th {
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
</style>
@endsection
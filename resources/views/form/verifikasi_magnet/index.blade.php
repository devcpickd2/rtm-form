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

    {{-- Alert error (validasi) --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-list-check"></i> Data Verifikasi Magnet Trap</h3>
                <a href="{{ route('verifikasi_magnet.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>

            {{-- Filter dan Search (server-side, otomatis submit dengan debounce) --}}
            <form id="filterForm" method="GET" action="{{ route('verifikasi_magnet.index') }}"
            class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

            <div class="input-group" style="max-width: 220px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-calendar-date text-muted"></i>
                </span>
                <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                value="{{ request('date') }}" placeholder="Tanggal Produksi">
            </div>

            <div class="input-group flex-grow-1" style="max-width: 350px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="search" id="search" class="form-control border-start-0"
                value="{{ request('search') }}" placeholder="Cari username, produksi, kode, produk...">
            </div>

            <div id="searchSpinner" class="d-none loading-status">
                <span class="loading-text">
                    Loading<span class="loading-dots"></span>
                </span>
            </div>

            @if(request('search') || request('date'))
            <div class="filter-reset">
                <a href="{{ route('verifikasi_magnet.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </a>
            </div>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>NO.</th>
                        <th>Date | Shift</th>
                        <th>Pukul</th>
                        <th>Nama Produk</th>
                        <th>Kode Produksi</th>
                        <th>Bukti</th>
                        <th>Jumlah Temuan</th>
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
                            {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}
                        </td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}</td>
                        <td class="text-center align-middle">{{ $dep->nama_produk }}</td>
                        <td class="text-center align-middle">{{ $dep->kode_produksi }}</td>
                        <td class="text-center align-middle">
                            @if($dep->bukti)
                            <a href="{{ asset('storage/' . $dep->bukti) }}" target="_blank">
                                <img 
                                src="{{ asset('storage/' . $dep->bukti) }}" 
                                alt="Bukti" 
                                style="max-width:100px; height:auto; cursor:pointer;"
                                loading="lazy"
                                >
                            </a>
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-center align-middle">{{ $dep->jumlah_temuan }}</td>
                        <td class="text-center align-middle">{{ $dep->keterangan }}</td>
                        <td class="text-center align-middle">{{ $dep->username }}</td>
                        <td class="text-center align-middle">{{ $dep->nama_produksi }}</td>
                        <td class="text-center align-middle">
                            @if ($dep->status_spv == 0)
                            <span class="fw-bold text-secondary">Created</span>
                            @elseif ($dep->status_spv == 1)
                            <span class="fw-bold text-success">Verified</span>
                            @elseif ($dep->status_spv == 2)
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revisionModal{{ $dep->uuid }}"
                             class="text-danger fw-bold text-decoration-none">Revision</a>

                             <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1"
                               aria-labelledby="revisionModalLabel{{ $dep->uuid }}" aria-hidden="true">
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
                    <td class="text-center align-middle">
                        <a href="{{ route('verifikasi_magnet.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                            <i class="bi bi-pencil"></i> Update
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center align-middle py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 1.5rem;"></i><br>
                        @if(request('search') || request('date'))
                        Tidak ada data yang cocok dengan pencarian/filter.
                        @else
                        Belum ada data verifikasi magnet trap.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Info jumlah data --}}
    @if($data->total() > 0)
    <div class="text-muted small mb-2">
        Menampilkan {{ $data->firstItem() }}-{{ $data->lastItem() }} dari {{ $data->total() }} data
    </div>
    @endif

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $data->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form        = document.getElementById('filterForm');
        const searchInput = document.getElementById('search');
        const dateInput   = document.getElementById('filter_date');
        const loading     = document.getElementById('searchSpinner');

        function showLoading() {
            loading.classList.remove('d-none');
        }

        // Search saat tekan Enter
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                showLoading();
                form.submit();
            }
        });

        // Filter tanggal
        dateInput.addEventListener('change', () => {
            showLoading();
            form.submit();
        });

        // Auto-hide alert setelah 3 detik
        const alert = document.querySelector('.alert');

        if (alert) {
            setTimeout(() => {
                alert.classList.remove('show');
                alert.classList.add('fade');
            }, 3000);
        }
    });
</script>
@endpush

@push('styles')
<style>
    .table td, .table th {
        font-size: 0.85rem;
        white-space: nowrap;
    }
    .text-danger {
        font-weight: bold;
    }
    .container {
        padding-left: 2px !important;
        padding-right: 2px !important;
    }
    .loading-status {
        margin-left: 8px;
        padding-left: 12px;
        border-left: 1px solid #dee2e6;
    }

    .loading-text {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        color: #6c757d;
        font-size: 0.8rem;
    }

    .filter-reset {
        margin-left: 4px;
        padding-left: 12px;
        border-left: 1px solid #dee2e6;
    }

    .loading-dots::after {
        content: '';
        animation: dots 1.2s steps(4, end) infinite;
    }

    @keyframes dots {
        0%   { content: ''; }
        25%  { content: '.'; }
        50%  { content: '..'; }
        75%  { content: '...'; }
        100% { content: ''; }
    }
</style>
@endpush
@endsection

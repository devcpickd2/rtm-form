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
                <h3><i class="bi bi-list-check"></i> Data Memo Retur</h3>
                <a href="{{ route('memo.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('memo.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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
                    value="{{ request('search') }}" placeholder="Search...">
                </div>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const searchInput = document.getElementById('search');
                    const dateInput   = document.getElementById('filter_date');
                    const rows        = document.querySelectorAll('#tableBody tr');

                    const filterTable = () => {
                        let search = searchInput.value.toLowerCase();
                        let date   = dateInput.value;

                        rows.forEach(row => {
                            let rowText = row.innerText.toLowerCase();    
                            let dateText = row.cells[1]?.innerText || "";  

                            let matchSearch = !search || rowText.includes(search);
                            let matchDate   = !date || dateText.includes(date);

                            row.style.display = (matchSearch && matchDate) ? "" : "none";
                        });
                    };

                    searchInput.addEventListener('input', filterTable);
                    dateInput.addEventListener('change', filterTable);
                });
            </script>

            {{-- Tambahkan table-responsive agar tabel tidak keluar border --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>From</th>
                            <th>Ke</th>
                            <th>Subject</th>
                            <th>Detail</th>
                            <th>QC</th>
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
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}</td>
                            <td class="text-center align-middle">{{ $dep->retur_dari }}</td>
                            <td class="text-center align-middle">{{ $dep->retur_ke }}</td>
                            <td class="text-center align-middle">{{ $dep->subject }}</td>
                            <td class="text-center align-middle">
                                <a href="#"
                                class="btn btn-outline-primary btn-sm fw-bold"
                                data-bs-toggle="modal"
                                data-bs-target="#detailRetur{{ $dep->uuid }}">
                                <i class="bi bi-eye-fill"></i> Detail
                            </a>

                            <div class="modal fade" id="detailRetur{{ $dep->uuid }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">

                                        <div class="modal-header bg-gradient bg-primary text-white border-0">
                                            <h5 class="modal-title">
                                                <i class="bi bi-file-earmark-text-fill me-2"></i>
                                                Detail Retur
                                            </h5>
                                            <button type="button"
                                            class="btn-close btn-close-white"
                                            data-bs-dismiss="modal">
                                        </button>
                                    </div>

                                    <div class="modal-body p-4 bg-light">

                                        <div class="bg-white rounded-4 shadow-sm p-4 border-start border-4 border-primary">

                                            <div class="d-flex align-items-center mb-3">
                                                <i class="bi bi-chat-left-text fs-4 text-primary me-2"></i>
                                                <span class="fw-bold text-secondary">
                                                    Isi Memo Retur
                                                </span>
                                            </div>

                                            <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit; text-align: left;">{{ $dep->detail_retur }}</pre>

                                        </div>

                                    </div>

                                    <div class="modal-footer border-0">
                                        <button type="button"
                                        class="btn btn-primary px-4"
                                        data-bs-dismiss="modal">
                                        Tutup
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-center align-middle">{{ $dep->username }}</td>
                <td class="text-center">
                    <a href="{{ route('memo.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                        <i class="bi bi-pencil"></i> Update
                    </a>
                    <form method="GET" action="{{ route('memo.exportPdf') }}" target="_blank">
                        <input type="hidden" name="uuid" value="{{ $dep->uuid }}">

                        <button type="submit" class="btn btn-danger btn-sm fw-bold shadow-sm ms-1">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="19" class="text-center align-middle">Belum ada data memo.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-3">
    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
</div>
</div>
</div>
</div>

{{-- Auto-hide alert setelah 3 detik --}}
<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if(alert){
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
</script>

{{-- CSS tambahan agar tabel lebih rapi --}}
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
    .modal-right .modal-dialog{
        position: fixed;
        right: 0;
        margin: 0;
        height: 100%;
        max-width: 500px;
    }

    .modal-right .modal-content{
        height: 100%;
        border-radius: 0;
    }
</style>
@endsection

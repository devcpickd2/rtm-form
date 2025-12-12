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
                <h3><i class="bi bi-list-check"></i> Data GMP Karyawan</h3>
                <a href="{{ route('gmp.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('gmp.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                <div class="input-group" style="max-width: 220px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar-date text-muted"></i>
                    </span>
                    <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                    value="{{ request('date') }}" placeholder="Tanggal Produksi">
                </div>

            <!--     <div class="input-group flex-grow-1" style="max-width: 350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" id="search" class="form-control border-start-0"
                    value="{{ request('search') }}" placeholder="Search...">
                </div> -->
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
                            <th>Date</th>
                            <th>Noodle & Rice</th>
                            <th>Cooking</th>
                            <th>Packing</th>
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
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}</td>   
                            @php
                            if (!function_exists('hitungPresentase')) {
                                function hitungPresentase($json) {
                                    if (!$json) return 0;

                                    $data = is_array($json) ? $json : json_decode($json, true);
                                    if (!$data) return 0;

                                    $total = 0;
                                    $count = 0;

                                    foreach ($data as $row) {
                                        foreach ($row as $key => $val) {
                                            if ($key !== 'nama_karyawan') {
                                                $total++;
                                                if ($val == 1) $count++;
                                            }
                                        }
                                    }

                                    return $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                }
                            }

                            if (!function_exists('topKaryawan')) {
                                function topKaryawan($json, $limit = 3) {
                                    if (!$json) return [];

                                    $data = is_array($json) ? $json : json_decode($json, true);
                                    if (!$data) return [];

                                    $scores = [];
                                    foreach ($data as $row) {
                                        $nama = $row['nama_karyawan'] ?? 'Tanpa Nama';
                                        $count = 0;
                                        foreach ($row as $key => $val) {
                                            if ($key !== 'nama_karyawan' && $val == 1) $count++;
                                        }
                                        $scores[] = ['nama' => $nama, 'nilai' => $count];
                                    }

                                    usort($scores, function($a, $b) { return $b['nilai'] <=> $a['nilai']; });

                                        return array_slice($scores, 0, $limit);
                                    }
                                }
                                @endphp


                                {{-- Pemakaian di tabel --}}
                                <td>
                                    {{ hitungPresentase($dep->noodle_rice) }} %
                                    <br>
                                    <small>
                                        @foreach(topKaryawan($dep->noodle_rice) as $row)
                                        • {{ $row['nama'] }} ({{ $row['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>

                                <td>
                                    {{ hitungPresentase($dep->cooking) }} %
                                    <br>
                                    <small>
                                        @foreach(topKaryawan($dep->cooking) as $row)
                                        • {{ $row['nama'] }} ({{ $row['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>

                                <td>
                                    {{ hitungPresentase($dep->packing) }} %
                                    <br>
                                    <small>
                                        @foreach(topKaryawan($dep->packing) as $row)
                                        • {{ $row['nama'] }} ({{ $row['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>
                                
                                <td class="text-center align-middle">
                                    @if ($dep->status_produksi == 0)
                                    <span class="fw-bold text-secondary">Created</span>
                                    @elseif ($dep->status_produksi == 1)
                                    <!-- Link buka modal -->
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#checkedModal{{ $dep->uuid }}" 
                                        class="fw-bold text-success text-decoration-none" style="cursor: pointer; font-weight: bold;">Checked</a>

                                        <!-- Modal -->
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
                                    </td>

                                    <td class="text-center align-middle">
                                        @if ($dep->status_spv == 0)
                                        <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_spv == 1)
                                        <span class="fw-bold text-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                        <!-- Link buka modal -->
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revisionModal{{ $dep->uuid }}" 
                                           class="text-danger fw-bold text-decoration-none" style="cursor: pointer;">Revision</a>

                                           <!-- Modal -->
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
                                        <a href="{{ route('gmp.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('gmp.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="19" class="text-center">Belum ada data gmp karyawan.</td>
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
        /* Header tabel merah */
        .table thead {
            background-color: #dc3545 !important; /* merah gelap */
            color: #fff;
        }

/* Baris tabel stripe merah muda */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f8d7da; /* merah muda terang */
}

.table-striped tbody tr:nth-of-type(even) {
    background-color: #f5c2c7; /* merah muda agak gelap */
}

/* Hover baris merah gelap */
.table tbody tr:hover {
    background-color: #e4606d !important;
    color: #fff;
}

/* Border tabel merah */
.table-bordered th, .table-bordered td {
    border-color: #dc3545;
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
</style>
@endsection

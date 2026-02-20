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
                <h3><i class="bi bi-list-check"></i> Data Pemeriksaan Pemasakan Noodle</h3>
                <a href="{{ route('noodle.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('noodle.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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
                            <th>Nama Produk</th>
                            <th>Pemasakan Noodle</th>
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
                        @php
                        // ambil field decoding yg sudah disiapkan di controller
                        $noodle = $dep->noodle_decoded ?? [];
                        @endphp
                        <tr>
                            <td class="text-center align-middle">{{ $no++ }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}</td>
                            <td class="text-center align-middle">{{ $dep->nama_produk }}</td>
                            <td class="text-center align-middle">
                                @php
                                // Ambil data mixing yang sudah didecode di controller
                                $mixing = $dep->mixing_decoded ?? [];
                                @endphp

                                <a href="#" data-bs-toggle="modal" data-bs-target="#mixingModal{{ $dep->uuid }}"
                                   style="font-weight: bold; text-decoration: underline;">
                                   Hasil Mixing
                               </a>

                               <div class="modal fade" id="mixingModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="mixingModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg"> {{-- modal besar supaya tabel muat --}}
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title" id="mixingModalLabel{{ $dep->uuid }}">Detail Mixing</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if(count($mixing))
                                            <div class="table-responsive">
                                               <table class="table table-bordered table-sm text-left align-middle">
                                                <tbody>
                                                    <tr>
                                                        <th>Nama Produk</th>
                                                        @foreach($mixing as $item)
                                                        <td>{{ $item['nama_produk'] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <th>Kode Produksi</th>
                                                        @foreach($mixing as $item)
                                                        <td>{{ $item['kode_produksi'] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <th>Bahan Utama</th>
                                                        @foreach($mixing as $item)
                                                        <td>{{ $item['bahan_utama'] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <th>Kode Bahan</th>
                                                        @foreach($mixing as $item)
                                                        <td>{{ $item['kode_bahan'] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <th>Berat Bahan</th>
                                                        @foreach($mixing as $item)
                                                        <td>{{ $item['berat_bahan'] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                    {{-- Tambahkan field lain jika ada --}}
                                                </tbody>
                                            </table>
                                        </div>
                                        @else
                                        <p class="text-center text-muted">Belum ada data mixing.</p>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
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

                        <td class="text-center align-middle">
                            <a href="{{ route('noodle.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                                <i class="bi bi-pencil"></i> Update
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center align-middle">Belum ada data noodle.</td>
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
</style>
@endsection

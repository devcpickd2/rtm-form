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
                <h3><i class="bi bi-list-check"></i> Data Pemeriksaan Suhu Ruang</h3>
                <a href="{{ route('suhu.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('suhu.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                <div class="input-group" style="max-width: 220px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar-date text-muted"></i>
                    </span>
                    <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                    value="{{ request('date') }}">
                </div>

               <!--  <div class="input-group flex-grow-1" style="max-width: 350px;">
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
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}</td>

                            {{-- Chillroom 0-4 --}}
                            <td class="{{ cekRange($dep->chillroom,0,4) }} text-center align-middle">{{ $dep->chillroom ?? 'Belum dicek' }}</td>
                            {{-- CS1 -22 s/d -18 --}}
                            <td class="{{ cekRange($dep->cs_1,-22,-18) }} text-center align-middle">{{ $dep->cs_1 ?? 'Belum dicek' }}</td>
                            {{-- CS2 -22 s/d -18 --}}
                            <td class="{{ cekRange($dep->cs_2,-22,-18) }} text-center align-middle">{{ $dep->cs_2 ?? 'Belum dicek' }}</td>
                            {{-- Anteroom RM 8-10 --}}
                            <td class="{{ cekRange($dep->anteroom_rm,8,10) }} text-center align-middle">{{ $dep->anteroom_rm ?? 'Belum dicek' }}</td>
                            {{-- Seasoning Suhu 22-30 | RH <=75 --}}
                            <td>
                                <span class="{{ cekRange($dep->seasoning_suhu,22,30) }} text-center align-middle">{{ $dep->seasoning_suhu ?? 'Belum dicek' }}</span> | 
                                <span class="{{ cekRange($dep->seasoning_rh,0,75) }} text-center align-middle">{{ $dep->seasoning_rh ?? 'Belum dicek' }}</span>
                            </td>
                            {{-- Prep Room 9-15 --}}
                            <td class="{{ cekRange($dep->prep_room,9,15) }} text-center align-middle">{{ $dep->prep_room ?? 'Belum dicek' }}</td>
                            {{-- Cooking 20-30 --}}
                            <td class="{{ cekRange($dep->cooking,20,30) }} text-center align-middle">{{ $dep->cooking ?? 'Belum dicek' }}</td>
                            {{-- Filling 9-15 --}}
                            <td class="{{ cekRange($dep->filling,9,15) }} text-center align-middle">{{ $dep->filling ?? 'Belum dicek' }}</td>
                            {{-- Rice 20-30 --}}
                            <td class="{{ cekRange($dep->rice,20,30) }} text-center align-middle">{{ $dep->rice ?? 'Belum dicek' }}</td>
                            {{-- Noodle 20-30 --}}
                            <td class="{{ cekRange($dep->noodle,20,30) }} text-center align-middle">{{ $dep->noodle ?? 'Belum dicek' }}</td>
                            {{-- Topping 9-15 --}}
                            <td class="{{ cekRange($dep->topping,9,15) }} text-center align-middle">{{ $dep->topping ?? 'Belum dicek' }}</td>
                            {{-- Packing 9-15 --}}
                            <td class="{{ cekRange($dep->packing,9,15) }} text-center align-middle">{{ $dep->packing ?? 'Belum dicek' }}</td>
                            {{-- DS Suhu 20-30 | RH <=75 --}}
                            <td>
                                <span class="{{ cekRange($dep->ds_suhu,20,30) }} text-center align-middle">{{ $dep->ds_suhu ?? 'Belum dicek' }}</span> | 
                                <span class="{{ cekRange($dep->ds_rh,0,75) }} text-center align-middle">{{ $dep->ds_rh ?? 'Belum dicek' }}</span>
                            </td>
                            {{-- CS FG -20 s/d -18 --}}
                            <td class="{{ cekRange($dep->cs_fg,-20,-18) }} text-center align-middle">{{ $dep->cs_fg ?? 'Belum dicek' }}</td>
                            {{-- Anteroom FG 0-10 --}}
                            <td class="{{ cekRange($dep->anteroom_fg,0,10) }} text-center align-middle">{{ $dep->anteroom_fg ?? 'Belum dicek' }}</td>
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
                                    <a href="{{ route('suhu.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>
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

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
                <h3><i class="bi bi-list-check"></i> Data Pemeriksaan Suhu Produk setelah IQF</h3>
                <a href="{{ route('iqf.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('iqf.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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
                        <th>Pukul</th>
                        <th>Nama Produk</th>
                        <th>Kode Produksi</th>
                        <th>Std CT (°C)</th>
                        <th>Suhu Pusat Produk (°C)</th>
                        <th>Problem</th>
                        <th>Tindakan Koreksi</th>
                        <th>Catatan</th>
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
                        <td class="text-center align-middle">{{ $dep->nama_produk }}</td>
                        <td class="text-center align-middle">{{ $dep->kode_produksi }}</td>
                        <td class="text-center align-middle">{{ $dep->std_suhu }}</td>
                        {{-- Ambil suhu pusat hanya dari $dep ini --}}
                        @php
                        $suhu_pusat = $dep->suhu_pusat ?? [];

                        $values = [];
                        $kets   = [];
                        for ($i = 1; $i <= 10; $i++) {
                            $val = $suhu_pusat[$i-1]['value'] ?? null;
                            $ket = $suhu_pusat[$i-1]['ket'] ?? null;

                            $values[$i] = $val;
                            $kets[$i]   = $ket;
                        }
                        $numericVals = array_filter($values, fn($v) => is_numeric($v));
                        $avg = count($numericVals) ? array_sum($numericVals)/count($numericVals) : null;
                        @endphp

                        <td class="text-center align-middle">
                            @if(!empty($suhu_pusat))
                            <a href="#" data-bs-toggle="modal" data-bs-target="#suhuPusatModal{{ $dep->uuid }}" style="font-weight:bold;text-decoration:underline;">
                                Hasil Suhu Pusat
                            </a>

                            {{-- Modal detail suhu --}}
                            <div class="modal fade" id="suhuPusatModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="suhuPusatModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" style="max-width:90%;">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title" id="suhuPusatModalLabel{{ $dep->uuid }}">Detail Pemeriksaan Suhu IQF</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm text-center align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th colspan="11">Suhu Pusat (°C)</th>
                                                        </tr>
                                                        <tr>
                                                            @for($i=1;$i<=10;$i++)
                                                            <th>{{ $i }}</th>
                                                            @endfor
                                                            <th>Rata-rata</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            @php 
                                                            $std = is_numeric($dep->std_suhu) ? $dep->std_suhu : null;
                                                            @endphp
                                                            @for($i=1;$i<=10;$i++)
                                                            @php
                                                            $val = $values[$i];
                                                            $ket = $kets[$i];
                                                            @endphp
                                                            <td>
                                                                @if($val !== null && $std !== null && is_numeric($val) && $val > $std)
                                                                <strong class="text-danger">{{ $val }}</strong>
                                                                @elseif($val !== null)
                                                                <strong>{{ $val }}</strong>
                                                                @else
                                                                -
                                                                @endif

                                                                @if(!empty($ket))
                                                                <br><small class="text-muted">{{ $ket }}</small>
                                                                @endif
                                                            </td>
                                                            @endfor

                                                            {{-- Kolom rata-rata --}}
                                                            <td class="text-center align-middle">
                                                                @if($avg && $std && $avg > $std)
                                                                <strong class="text-danger">{{ number_format($avg,2) }}</strong>
                                                                @elseif($avg)
                                                                <strong>{{ number_format($avg,2) }}</strong>
                                                                @else
                                                                -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <span>-</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">{{ $dep->problem ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $dep->tindakan_koreksi ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $dep->catatan ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $dep->username ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $dep->nama_produksi ?? '-' }}</td>
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
                            </td>
 -->
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
                                <a href="{{ route('iqf.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                                    <i class="bi bi-pencil"></i> Update
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center align-middle">Belum ada data produk.</td>
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

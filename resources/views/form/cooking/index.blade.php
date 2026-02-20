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
                <h3><i class="bi bi-list-check"></i> Data Pemeriksaan Pemasakan Produk di Steam/Cooking Kettle</h3>
                <a href="{{ route('cooking.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('cooking.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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
                            <th>Jenis Produk</th>
                            <th>Kode Produksi</th>
                            <th>Waktu (Start - Stop)</th>
                            <th>Mesin</th>
                            <th>Pemasakan</th>
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
                            <td class="text-center align-middle">{{ $dep->nama_produk }} ({{ $dep->sub_produk }})</td>
                            <td class="text-center align-middle">{{ $dep->jenis_produk }}</td>
                            <td class="text-center align-middle">{{ $dep->kode_produksi }}</td>
                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($dep->waktu_mulai)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($dep->waktu_selesai)->format('H:i') }}
                            </td>
                            <td class="text-center align-middle">
                                @php
                                // decode json nama_mesin jadi array
                                $namaMesin = is_array($dep->nama_mesin)
                                ? $dep->nama_mesin
                                : json_decode($dep->nama_mesin, true);
                                if (!$namaMesin) $namaMesin = [];
                                @endphp

                                {{-- tampilkan sebagai list koma --}}
                                {{ implode(', ', $namaMesin) }}
                            </td>
                            <td class="text-center align-middle">
                                @php
                                $pemasakan = $dep->pemasakan_decoded ?? [];
                                @endphp

                                <a href="#" data-bs-toggle="modal" data-bs-target="#pemasakanModal{{ $dep->uuid }}"
                                 style="font-weight: bold; text-decoration: underline;">
                                 Detail
                             </a>

                             <div class="modal fade" id="pemasakanModal{{ $dep->uuid }}" tabindex="-1"
                                aria-labelledby="pemasakanModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title" id="pemasakanModalLabel{{ $dep->uuid }}">Detail Pemasakan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <b>
                                                <label>Nama Produk : {{ $dep->nama_produk }} ({{ $dep->sub_produk }})</label><br>
                                                <label>Kode Produksi : {{ $dep->kode_produksi }}</label></b>
                                                @if(count($pemasakan))
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm align-middle text-center" style="border-collapse: collapse;">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Pukul</th>
                                                                <th>Tahapan Proses</th>
                                                                <th>Jenis Bahan</th>
                                                                <th>Kode Bahan</th>
                                                                <th>Jumlah Standar</th>
                                                                <th>Jumlah Aktual</th>
                                                                <th>Sensori</th>
                                                                <th>Lama Proses</th>
                                                                <th>Mixing Paddle On</th>
                                                                <th>Mixing Paddle Off</th>
                                                                <th>Pressure</th>
                                                                <th>Temperature</th>
                                                                <th>Target Temp</th>
                                                                <th>Actual Temp</th>
                                                                <th>Suhu Pusat</th>
                                                                <th>Warna</th>
                                                                <th>Aroma</th>
                                                                <th>Rasa</th>
                                                                <th>Tekstur</th>
                                                                <th>Catatan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($pemasakan as $item)
                                                            @php
                                                            $max = max(
                                                            count($item['jenis_bahan'] ?? []),
                                                            count($item['kode_bahan'] ?? []),
                                                            count($item['jumlah_standar'] ?? []),
                                                            count($item['jumlah_aktual'] ?? []),
                                                            count($item['sensori'] ?? [])
                                                            );
                                                            @endphp

                                                            @for($i = 0; $i < $max; $i++)
                                                            <tr>
                                                                {{-- tampilkan hanya di baris pertama --}}
                                                                @if($i == 0)
                                                                <td rowspan="{{ $max }}">{{ $item['pukul'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $item['tahapan'] ?? '-' }}</td>
                                                                @endif

                                                                <td>{{ $item['jenis_bahan'][$i] ?? '-' }}</td>
                                                                <td>{{ $item['kode_bahan'][$i] ?? '-' }}</td>
                                                                <td>{{ $item['jumlah_standar'][$i] ?? '-' }}</td>
                                                                <td>{{ $item['jumlah_aktual'][$i] ?? '-' }}</td>
                                                                <td>{{ (isset($item['sensori'][$i]) && $item['sensori'][$i] === 'Oke') ? 'Oke' : '-' }}</td>

                                                                @if($i == 0)
                                                                <td rowspan="{{ $max }}">{{ $item['lama_proses'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($item['paddle_on']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($item['paddle_off']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $item['pressure'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $item['temperature'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">
                                                                    {{ $item['target_temp_operator'] ?? '' }} {{ $item['target_temp'] ?? '-' }} °C
                                                                </td>
                                                                <td rowspan="{{ $max }}">{{ $item['actual_temp'] ?? '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $item['suhu_pusat'] ?? '-' }} ({{ $item['suhu_pusat_menit'] ?? '' }} Menit)</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($item['warna']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($item['aroma']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($item['rasa']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ !empty($item['tekstur']) ? 'Oke' : '-' }}</td>
                                                                <td rowspan="{{ $max }}">{{ $item['catatan'] ?? '-' }}</td>
                                                                @endif
                                                            </tr>
                                                            @endfor
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @else
                                                <p class="text-center text-muted">Belum ada data pemasakan.</p>
                                                @endif
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                           <!--  <td class="text-center align-middle">
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
                                <td class="text-center align-middle">{{ $dep->username }}</td>
                                <td class="text-center align-middle">{{ $dep->nama_produksi }}</td>
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
                                    <a href="{{ route('cooking.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="19" class="text-center align-middle">Belum ada data pemasakan.</td>
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
/* container tiap sel yang berisi baris-baris kecil */
.inner-cell {
    display: flex;
    flex-direction: column;
    align-items: stretch;
}

/* tiap baris kecil di dalam sel */
.inner-row {
    border-bottom: 1px solid #e0e0e0;
    padding: 4px 8px;
    min-height: 22px; /* atur agar cukup tinggi */
    box-sizing: border-box;
    font-size: 13px;
    background-color: #ffffff;
}

/* agar boundary antar main-row tegas */
.table tbody tr.main-row {
    border-bottom: 2px solid #bfc3c7;
}

/* hover tetap enak dilihat */
.table tbody tr.main-row:hover .inner-row {
    background-color: #f8f9fa;
}

</style>
@endsection

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
                <h3><i class="bi bi-list-check"></i> Data Tahapan Suhu Produk Setiap Tahapan Proses</h3>
                <a href="{{ route('tahapan.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>
            </div>
            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('tahapan.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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
                        <th rowspan="2">NO.</th>
                        <th rowspan="2">Date | Shift</th>
                        <th rowspan="2">Nama Produk</th>
                        <th rowspan="2">Kode Produksi</th>
                        <th colspan="8">JAM MULAI</th>
                        <th>SUHU PRODUK (°C)</th>
                        <th rowspan="2">Catatan</th>
                        <th rowspan="2">QC</th>
                        <th rowspan="2">Produksi</th>
                        <th rowspan="2">SPV</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>Filling/Portioning</th>
                        <th>IQF</th>
                        <th>Top Sealer</th>
                        <th>X-Ray</th>
                        <th>Sticker</th>
                        <th>Shrink</th>
                        <th>Packing Box</th>
                        <th>Cold Storage</th>

                        <th>Result Suhu</th>
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
                        <td class="text-center align-middle">{{ $dep->nama_produk }}</td>
                        <td class="text-center align-middle">{{ $dep->kode_produksi }}</td>
                        <td class="text-center align-middle">
                            {{ $dep->filling_mulai ? \Carbon\Carbon::parse($dep->filling_mulai)->format('H:i') : '-' }} -
                            {{ $dep->filling_selesai ? \Carbon\Carbon::parse($dep->filling_selesai)->format('H:i') : '-' }}
                        </td>
                        <td class="text-center align-middle">{{ $dep->waktu_iqf ? \Carbon\Carbon::parse($dep->waktu_iqf)->format('H:i') : '-' }}</td>
                        <td class="text-center align-middle">{{ $dep->waktu_sealer ? \Carbon\Carbon::parse($dep->waktu_sealer)->format('H:i') : '-' }}</td>
                        <td class="text-center align-middle">{{ $dep->waktu_xray ? \Carbon\Carbon::parse($dep->waktu_xray)->format('H:i') : '-' }}</td>
                        <td class="text-center align-middle">{{ $dep->waktu_sticker ? \Carbon\Carbon::parse($dep->waktu_sticker)->format('H:i') : '-' }}</td>
                        <td class="text-center align-middle">{{ $dep->waktu_shrink ? \Carbon\Carbon::parse($dep->waktu_shrink)->format('H:i') : '-' }}</td>
                        <td class="text-center align-middle">{{ $dep->waktu_packing ? \Carbon\Carbon::parse($dep->waktu_packing)->format('H:i') : '-' }}</td>
                        <td class="text-center align-middle">{{ $dep->waktu_cs ? \Carbon\Carbon::parse($dep->waktu_cs)->format('H:i') : '-' }}</td>
                        <td class="text-center align-middle">
                            @php
                            // Decode suhu Filling
                            $suhu_filling = $dep->suhu_filling;
                            if(is_string($suhu_filling)) {
                                $decoded = json_decode($suhu_filling, true);
                                $suhu_filling = is_array($decoded) ? $decoded : [];
                            } elseif(!is_array($suhu_filling)) {
                                $suhu_filling = [];
                            }

                            // Decode suhu lainnya
                            $suhuFields = ['suhu_masuk_iqf','suhu_keluar_iqf','suhu_sealer','suhu_xray','suhu_sticker','suhu_shrink'];
                            $suhuData = [];
                            foreach($suhuFields as $field) {
                                $value = $dep->$field;
                                if(is_string($value)) {
                                    $decoded = json_decode($value, true);
                                    $suhuData[$field] = is_array($decoded) ? $decoded : [];
                                } else {
                                    $suhuData[$field] = [];
                                }
                            }
                            @endphp

                            @if(!empty($suhu_filling) || array_filter($suhuData) || $dep->downtime || $dep->suhu_cs)
                            <a href="#" data-bs-toggle="modal" data-bs-target="#suhuModal{{ $dep->uuid }}" style="font-weight: bold; text-decoration: underline;">Result</a>

                            <div class="modal fade" id="suhuModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="suhuModalLabel{{ $dep->uuid }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title" id="suhuModalLabel{{ $dep->uuid }}">Detail Pengecekan Suhu Produk</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm mb-0 text-center">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th rowspan="2">No</th>
                                                            <th rowspan="2">Filling</th>
                                                            <th colspan="6">Tahapan Lainnya</th>
                                                            <th rowspan="2">Downtime</th>
                                                            <th rowspan="2">Cold Storage</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Masuk IQF</th>
                                                            <th>Keluar IQF</th>
                                                            <th>Top Sealer</th>
                                                            <th>X-Ray</th>
                                                            <th>Sticker</th>
                                                            <th>Shrink</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @for($i=0; $i<6; $i++)
                                                        <tr>
                                                            <td>{{ $i+1 }}</td>
                                                            <td>{{ $suhu_filling[$i]['nama_bahan'] ?? '-' }}: {{ $suhu_filling[$i]['suhu'] ?? '-' }}°C</td>
                                                            <td>{{ $suhuData['suhu_masuk_iqf'][$i] ?? '-' }}°C</td>
                                                            <td>{{ $suhuData['suhu_keluar_iqf'][$i] ?? '-' }}°C</td>
                                                            <td>{{ $suhuData['suhu_sealer'][$i] ?? '-' }}°C</td>
                                                            <td>{{ $suhuData['suhu_xray'][$i] ?? '-' }}°C</td>
                                                            <td>{{ $suhuData['suhu_sticker'][$i] ?? '-' }}°C</td>
                                                            <td>{{ $suhuData['suhu_shrink'][$i] ?? '-' }}°C</td>
                                                            <td>{{ $i===0 ? ($dep->downtime ?? '-') : '' }}</td>
                                                            <td>{{ $i===0 ? ($dep->suhu_cs ?? '-') : '' }}</td>
                                                        </tr>
                                                        @endfor
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
                        <td class="text-center align-middle">{{ $dep->keterangan ?? '-' }}</td>
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
                                <a href="{{ route('tahapan.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                                    <i class="bi bi-pencil"></i> Update
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="25" class="text-center align-middle">Belum ada data proses.</td>
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

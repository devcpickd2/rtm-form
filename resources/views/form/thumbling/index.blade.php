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
                <h3><i class="bi bi-list-check"></i> Data Pemeriksaan Proses Thumbling</h3>
                <a href="{{ route('thumbling.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah
                </a>

                                                  <!--  <button type="button" class="btn btn-success"
                onclick="showMaintenanceWarning()">
                <i class="bi bi-plus-circle"></i> Tambah
            </button> -->
        </div>

        {{-- Filter dan Live Search --}}
        <form id="filterForm" method="GET" action="{{ route('thumbling.index') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>NO.</th>
                        <th>Date | Shift</th>
                        <th>Nama Produk</th>
                        <th>Thumbling</th>
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
                        <td class="text-center align-middle">{{ $dep->nama_produk }}</td>    

                        <td class="text-center align-middle">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#thumblingModal{{ $dep->uuid }}" class="fw-bold text-decoration-underline">
                                Detail Thumbling
                            </a>

                            <div class="modal fade" id="thumblingModal{{ $dep->uuid }}" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title text-start">Detail Thumbling</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 250px; text-align:left;">Parameter</th>
                                                            <th style="text-align:left;">Data</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- PROSES THUMBLING --}}
                                                        <tr class="table-secondary">
                                                            <td colspan="2" class="text-start"><strong>PROSES THUMBLING</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Batch No.</td>
                                                            <td class="text-start">{{ $dep->kode_produksi ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Identifikasi Daging</td>
                                                            <td class="text-start">{{ $dep->identifikasi_daging ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Asal Daging</td>
                                                            <td class="text-start">{{ $dep->asal_daging ?? '-' }}</td>
                                                        </tr>

                                                        {{-- KODE DAGING --}}
                                                        <tr>
                                                            <td class="text-start">Kode Daging</td>
                                                            <td class="text-start">
                                                                @if(!empty($dep->kode_daging))
                                                                <table class="table table-bordered table-sm mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Kode</th>
                                                                            <th>Berat (kg)</th>
                                                                            <th>Suhu (0-10°C)</th>
                                                                            <th>Rata-rata</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($dep->kode_daging as $i => $kd)
                                                                        <tr>
                                                                            <td>{{ $kd ?? '-' }}</td>
                                                                            <td>{{ $dep->berat_daging[$i] ?? '-' }}</td>
                                                                            <td>{{ implode(', ', $dep->suhu_daging[$i] ?? []) }}</td>
                                                                            <td>{{ $dep->rata_daging[$i] ?? '-' }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                @else
                                                                -
                                                                @endif
                                                            </td>
                                                        </tr>

                                                        {{-- MARINADE / BAHAN UTAMA --}}
                                                        <tr>
                                                            <td class="text-start">Bahan Utama</td>
                                                            <td class="text-start">
                                                                @if(!empty($dep->premix))
                                                                <table class="table table-bordered table-sm mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Bahan</th>
                                                                            <th>Kode</th>
                                                                            <th>Berat (kg)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($dep->premix as $i => $p)
                                                                        <tr>
                                                                            <td>{{ $p ?? '-' }}</td>
                                                                            <td>{{ $dep->kode_premix[$i] ?? '-' }}</td>
                                                                            <td>{{ $dep->berat_premix[$i] ?? '-' }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                @else
                                                                -
                                                                @endif
                                                            </td>
                                                        </tr>

                                                        {{-- BAHAN LAIN --}}
                                                        <tr>
                                                            <td class="text-start">Bahan Lain</td>
                                                            <td class="text-start">
                                                                @if(!empty($dep->bahan_lain))
                                                                <table class="table table-bordered table-sm mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Bahan</th>
                                                                            <th>Kode</th>
                                                                            <th>Berat (kg)</th>
                                                                            <th>Sensori</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($dep->bahan_lain as $bl)
                                                                        <tr>
                                                                            <td>{{ $bl['premix'] ?? '-' }}</td>
                                                                            <td>{{ $bl['kode'] ?? '-' }}</td>
                                                                            <td>{{ $bl['berat'] ?? '-' }}</td>
                                                                            <td>{{ $bl['sens'] ?? '-' }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                @else
                                                                -
                                                                @endif
                                                            </td>
                                                        </tr>

                                                        {{-- PARAMETER CAIRAN --}}
                                                        <tr class="table-secondary">
                                                            <td colspan="2" class="text-start"><strong>PARAMETER CAIRAN</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Air (kg)</td>
                                                            <td class="text-start">{{ $dep->air ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Suhu Air (°C)</td>
                                                            <td class="text-start">{{ $dep->suhu_air ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Suhu Marinade (°C)</td>
                                                            <td class="text-start">{{ $dep->suhu_marinade ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Lama Pengadukan (menit)</td>
                                                            <td class="text-start">{{ $dep->lama_pengadukan ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Marinade Brix – Salinity</td>
                                                            <td class="text-start">{{ $dep->marinade_brix_salinity ?? '-' }}</td>
                                                        </tr>

                                                        {{-- PARAMETER THUMBLING --}}
                                                        <tr class="table-secondary">
                                                            <td colspan="2" class="text-start"><strong>PARAMETER THUMBLING</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Drum On (Menit)</td>
                                                            <td class="text-start">{{ $dep->drum_on ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Drum Off (Menit)</td>
                                                            <td class="text-start">{{ $dep->drum_off ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Drum Speed (RPM)</td>
                                                            <td class="text-start">{{ $dep->drum_speed ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Vacuum Time (Menit)</td>
                                                            <td class="text-start">{{ $dep->vacuum_time ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Total Time (Menit)</td>
                                                            <td class="text-start">{{ $dep->total_time ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Mulai – Selesai</td>
                                                            <td class="text-start">{{ $dep->waktu_mulai ?? '-' }} – {{ $dep->waktu_selesai ?? '-' }}</td>
                                                        </tr>

                                                        {{-- HASIL THUMBLING --}}
                                                        <tr class="table-secondary">
                                                            <td colspan="2" class="text-start"><strong>HASIL THUMBLING</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Suhu Daging</td>
                                                            <td class="text-start">
                                                                @if(!empty($dep->suhu_daging_thumbling))
                                                                <table class="table table-bordered table-sm mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            @foreach($dep->suhu_daging_thumbling as $i => $suhu)
                                                                            <th>Suhu {{ $i+1 }}</th>
                                                                            @endforeach
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            @foreach($dep->suhu_daging_thumbling as $suhu)
                                                                            <td>{{ $suhu }}</td>
                                                                            @endforeach
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <strong>Rata-rata:</strong> {{ $dep->rata_daging_thumbling ?? '-' }}
                                                                @else
                                                                -
                                                                @endif
                                                            </td>
                                                        </tr>

                                                        {{-- KONDISI & CATATAN --}}
                                                        <tr class="table-secondary">
                                                            <td colspan="2" class="text-start"><strong>KONDISI & CATATAN</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Kondisi Akhir</td>
                                                            <td class="text-start">{{ $dep->kondisi_daging_akhir ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">Catatan Akhir</td>
                                                            <td class="text-start">{{ $dep->catatan_akhir ?? '-' }}</td>
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

                            {{-- Status SPV --}}
                            <td class="text-center align-middle">
                                @if ($dep->status_spv == 0)
                                <span class="fw-bold text-secondary">Created</span>
                                @elseif ($dep->status_spv == 1)
                                <span class="fw-bold text-success">Verified</span>
                                @elseif ($dep->status_spv == 2)
                                <span class="fw-bold text-danger">Revision</span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="text-center align-middle">
                                <a href="{{ route('thumbling.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

<!-- <button type="button" class="btn btn-warning btn-sm me-1"
                                onclick="showMaintenanceWarning()">
                                <i class="bi bi-pencil"></i> Edit
                            </button> -->
                            <form action="{{ route('thumbling.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data thumbling.</td>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showMaintenanceWarning() {
        Swal.fire({
            icon: 'info',
            title: 'Halaman Sedang Maintenance',
            text: 'Maaf, fitur Ini sedang tidak tersedia untuk sementara waktu.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
        });
    }
</script>

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

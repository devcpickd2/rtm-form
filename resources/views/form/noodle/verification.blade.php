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
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('noodle.verification') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Verification</th>
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
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}</td>   
                            <td>{{ $dep->nama_produk }}</td>
                            <td class="text-center">
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

                    <td>{{ $dep->nama_produksi }}</td>
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
                        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
                            <i class="bi bi-shield-check me-1"></i> Verifikasi
                        </button>

                        <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $dep->uuid }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-md">
                                <form action="{{ route('noodle.verification.update', $dep->uuid) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white" 
                                    style="background: linear-gradient(145deg, #7a1f12, #9E3419); 
                                    box-shadow: 0 15px 40px rgba(0,0,0,0.5);">
                                    <div class="modal-header border-bottom border-light-subtle p-4" style="border-bottom-width: 3px !important;">
                                        <h5 class="modal-title fw-bolder fs-3 text-uppercase" id="verifyModalLabel{{ $dep->uuid }}" style="color: #00ffc4;">
                                            <i class="bi bi-gear-fill me-2"></i> VERIFICATION
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body p-5">
                                        <p class="text-light mb-4 fs-6">
                                            Pastikan data yang akan diverifikasi di check dengan teliti terlebih dahulu.
                                        </p>
                                        <div class="row g-4">
                                            <div class="col-md-12">
                                                <label for="status_spv_{{ $dep->uuid }}" class="form-label fw-bold mb-2 text-center d-block" 
                                                 style="color: #FFE5DE; font-size: 0.95rem;">
                                                 Pilih Status Verifikasi
                                             </label>

                                             <select 
                                             name="status_spv" 
                                             id="status_spv_{{ $dep->uuid }}" 
                                             class="form-select form-select-lg fw-bold text-center mx-auto"
                                             style="
                                             background: linear-gradient(135deg, #fff1f0, #ffe5de);
                                             border: 2px solid #dc3545;
                                             border-radius: 12px;
                                             color: #dc3545;
                                             height: 55px;
                                             font-size: 1.1rem;
                                             box-shadow: 0 6px 12px rgba(0,0,0,0.1);
                                             width: 85%;
                                             transition: all 0.3s ease;
                                             "
                                             required
                                             >
                                             <option value="1" {{ $dep->status_spv == 1 ? 'selected' : '' }} 
                                                 style="color: #198754; font-weight: 600;">✅ Verified (Disetujui)</option>
                                                 <option value="2" {{ $dep->status_spv == 2 ? 'selected' : '' }} 
                                                     style="color: #dc3545; font-weight: 600;">❌ Revision (Perlu Perbaikan)</option>
                                                 </select>
                                             </div>

                                             <div class="col-md-12 mt-3">
                                                <label for="catatan_spv_{{ $dep->uuid }}" class="form-label fw-bold text-light mb-2">
                                                    Catatan Tambahan (Opsional)
                                                </label>
                                                <textarea name="catatan_spv" id="catatan_spv_{{ $dep->uuid }}" rows="4" 
                                                  class="form-control text-dark border-0 shadow-none" 
                                                  placeholder="Masukkan catatan, misalnya alasan revisi..." 
                                                  style="background-color: #FFE5DE; height: 120px;">
                                                  {{ $dep->catatan_spv }}
                                              </textarea>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="modal-footer justify-content-end p-4 border-top" style="background-color: #9E3419; border-color: #00ffc4 !important;">
                                    <button type="button" class="btn btn-outline-light fw-bold rounded-pill px-4 me-2" data-bs-dismiss="modal">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn fw-bolder rounded-pill px-5" style="background-color: #E39581; color: #2c3e50;">
                                        <i class="bi bi-save-fill me-1"></i> SUBMIT
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center">Belum ada data noodle.</td>
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

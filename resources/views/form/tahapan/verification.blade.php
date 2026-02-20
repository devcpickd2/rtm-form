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
                <a href="{{ route('tahapan.recyclebin') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-trash"></i> Recycle Bin
                </a>
            </div>

            {{-- Filter dan Live Search --}}
            <form id="filterForm" method="GET" action="{{ route('tahapan.verification') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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
                        <th rowspan="2">Verification</th>
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
                        <td class="text-center align-middle">
                            @if ($dep->status_spv == 0)
                            <span class="fw-bold text-secondary">Created</span>
                            @elseif ($dep->status_spv == 1)
                            <span class="fw-bold text-success">Verified</span>
                            @elseif ($dep->status_spv == 2)
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revisionModal{{ $dep->uuid }}" 
                             class="text-danger fw-bold text-decoration-none" style="cursor: pointer;">Revision</a>
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
                            <form action="{{ route('tahapan.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>

                        <div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $dep->uuid }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-md">
                                <form action="{{ route('tahapan.verification.update', $dep->uuid) }}" method="POST">
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

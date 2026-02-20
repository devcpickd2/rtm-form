@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Thumbling</h3>
                <a href="{{ route('thumbling.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Nama Produk</th>
                            <th>Thumbling</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($thumbling as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#thumblingModal{{ $item->uuid }}" class="fw-bold text-decoration-underline">
                                    Detail Thumbling
                                </a>

                                <div class="modal fade" id="thumblingModal{{ $item->uuid }}" tabindex="-1">
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
                                                                <td class="text-start">{{ $item->kode_produksi ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Identifikasi Daging</td>
                                                                <td class="text-start">{{ $item->identifikasi_daging ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Asal Daging</td>
                                                                <td class="text-start">{{ $item->asal_daging ?? '-' }}</td>
                                                            </tr>

                                                            {{-- KODE DAGING --}}
                                                            <tr>
                                                                <td class="text-start">Kode Daging</td>
                                                                <td class="text-start">
                                                                    @if(!empty($item->kode_daging))
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
                                                                            @foreach($item->kode_daging as $i => $kd)
                                                                            <tr>
                                                                                <td>{{ $kd ?? '-' }}</td>
                                                                                <td>{{ $item->berat_daging[$i] ?? '-' }}</td>
                                                                                <td>{{ implode(', ', $item->suhu_daging[$i] ?? []) }}</td>
                                                                                <td>{{ $item->rata_daging[$i] ?? '-' }}</td>
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
                                                                    @if(!empty($item->premix))
                                                                    <table class="table table-bordered table-sm mb-0">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th>Bahan</th>
                                                                                <th>Kode</th>
                                                                                <th>Berat (kg)</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($item->premix as $i => $p)
                                                                            <tr>
                                                                                <td>{{ $p ?? '-' }}</td>
                                                                                <td>{{ $item->kode_premix[$i] ?? '-' }}</td>
                                                                                <td>{{ $item->berat_premix[$i] ?? '-' }}</td>
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
                                                                    @if(!empty($item->bahan_lain))
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
                                                                            @foreach($item->bahan_lain as $bl)
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
                                                                <td class="text-start">{{ $item->air ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Suhu Air (°C)</td>
                                                                <td class="text-start">{{ $item->suhu_air ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Suhu Marinade (°C)</td>
                                                                <td class="text-start">{{ $item->suhu_marinade ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Lama Pengadukan (menit)</td>
                                                                <td class="text-start">{{ $item->lama_pengadukan ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Marinade Brix – Salinity</td>
                                                                <td class="text-start">{{ $item->marinade_brix_salinity ?? '-' }}</td>
                                                            </tr>

                                                            {{-- PARAMETER THUMBLING --}}
                                                            <tr class="table-secondary">
                                                                <td colspan="2" class="text-start"><strong>PARAMETER THUMBLING</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Drum On (Menit)</td>
                                                                <td class="text-start">{{ $item->drum_on ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Drum Off (Menit)</td>
                                                                <td class="text-start">{{ $item->drum_off ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Drum Speed (RPM)</td>
                                                                <td class="text-start">{{ $item->drum_speed ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Vacuum Time (Menit)</td>
                                                                <td class="text-start">{{ $item->vacuum_time ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Total Time (Menit)</td>
                                                                <td class="text-start">{{ $item->total_time ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Mulai – Selesai</td>
                                                                <td class="text-start">{{ $item->waktu_mulai ?? '-' }} – {{ $item->waktu_selesai ?? '-' }}</td>
                                                            </tr>

                                                            {{-- HASIL THUMBLING --}}
                                                            <tr class="table-secondary">
                                                                <td colspan="2" class="text-start"><strong>HASIL THUMBLING</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Suhu Daging</td>
                                                                <td class="text-start">
                                                                    @if(!empty($item->suhu_daging_thumbling))
                                                                    <table class="table table-bordered table-sm mb-0">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                @foreach($item->suhu_daging_thumbling as $i => $suhu)
                                                                                <th>Suhu {{ $i+1 }}</th>
                                                                                @endforeach
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                @foreach($item->suhu_daging_thumbling as $suhu)
                                                                                <td>{{ $suhu }}</td>
                                                                                @endforeach
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    <strong>Rata-rata:</strong> {{ $item->rata_daging_thumbling ?? '-' }}
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
                                                                <td class="text-start">{{ $item->kondisi_daging_akhir ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Catatan Akhir</td>
                                                                <td class="text-start">{{ $item->catatan_akhir ?? '-' }}</td>
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
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('thumbling.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('thumbling.deletePermanent', $item->uuid) }}" 
                                  method="POST" class="d-inline">
                                  @csrf
                                  @method('DELETE')
                                  <button class="btn btn-danger btn-sm mb-1"
                                  onclick="return confirm('Hapus permanen?')">
                                  <i class="bi bi-x-circle"></i> Delete
                              </button>
                          </form>
                      </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8" class="text-center align-middle">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $thumbling->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection

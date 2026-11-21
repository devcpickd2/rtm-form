
@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-shield-check"></i>
                    Audit Pemeriksaan Proses Thumbling
                </h3>

                <a href="{{ route('thumbling.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <form method="GET"
                  action="{{ route('thumbling.audit') }}"
                  class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <input type="date"
                       name="date"
                       class="form-control"
                       style="max-width:220px"
                       value="{{ request('date') }}">

                <input type="text"
                       name="search"
                       class="form-control"
                       style="max-width:350px"
                       placeholder="Cari produk, kode produksi, username..."
                       value="{{ request('search') }}">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('thumbling.audit') }}" class="btn btn-secondary">
                    Reset
                </a>
            </form>

            <div class="alert alert-info">
                Data Audit adalah salinan kondisi data Thumbling saat pertama kali disimpan.
                Data ini hanya dapat dilihat dan tidak dapat diubah.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Identifikasi Daging</th>
                            <th>Asal Daging</th>
                            <th>Rata-rata Daging</th>
                            <th>Kondisi Daging</th>
                            <th>Air</th>
                            <th>Suhu Air</th>
                            <th>Suhu Marinade</th>
                            <th>Lama Pengadukan</th>
                            <th>Brix / Salinity</th>
                            <th>Drum On</th>
                            <th>Drum Off</th>
                            <th>Drum Speed</th>
                            <th>Vacuum Time</th>
                            <th>Total Time</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Rata-rata Suhu Thumbling</th>
                            <th>Kondisi Daging Akhir</th>
                            <th>Catatan Akhir</th>
                            <th>Catatan</th>
                            <th>Produksi</th>
                            <th>Status Produksi</th>
                            <th>SPV</th>
                            <th>Status SPV</th>
                            <th>Catatan SPV</th>
                            <th>Detail Bahan</th>
                            <th>Waktu Snapshot</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="text-center">
                                    {{ $item->date ? $item->date->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">{{ $item->shift ?? '-' }}</td>
                                <td>{{ $item->nama_produk ?? '-' }}</td>
                                <td>{{ $item->kode_produksi ?? '-' }}</td>
                                <td>{{ $item->identifikasi_daging ?? '-' }}</td>
                                <td>{{ $item->asal_daging ?? '-' }}</td>
                                <td class="text-center">{{ $item->rata_daging_thumbling ?? '-' }}</td>
                                <td>{{ $item->kondisi_daging ?? '-' }}</td>
                                <td class="text-center">{{ $item->air ?? '-' }}</td>
                                <td class="text-center">{{ $item->suhu_air ?? '-' }}</td>
                                <td class="text-center">{{ $item->suhu_marinade ?? '-' }}</td>
                                <td class="text-center">{{ $item->lama_pengadukan ?? '-' }}</td>
                                <td>{{ $item->marinade_brix_salinity ?? '-' }}</td>
                                <td class="text-center">{{ $item->drum_on ?? '-' }}</td>
                                <td class="text-center">{{ $item->drum_off ?? '-' }}</td>
                                <td class="text-center">{{ $item->drum_speed ?? '-' }}</td>
                                <td class="text-center">{{ $item->vacuum_time ?? '-' }}</td>
                                <td class="text-center">{{ $item->total_time ?? '-' }}</td>
                                <td class="text-center">{{ $item->waktu_mulai ?? '-' }}</td>
                                <td class="text-center">{{ $item->waktu_selesai ?? '-' }}</td>
                                <td class="text-center">{{ $item->rata_daging_thumbling ?? '-' }}</td>
                                <td>{{ $item->kondisi_daging_akhir ?? '-' }}</td>
                                <td>{{ $item->catatan_akhir ?? '-' }}</td>
                                <td>{{ $item->catatan ?? '-' }}</td>
                                <td>{{ $item->nama_produksi ?? '-' }}</td>

                                <td class="text-center">
                                    {{ $item->status_produksi ?? '-' }}
                                </td>

                                <td>{{ $item->nama_spv ?? '-' }}</td>

                                <td class="text-center">
                                    @if ($item->status_spv == 1)
                                        <span class="badge bg-success">Verified</span>
                                    @elseif ($item->status_spv == 2)
                                        <span class="badge bg-danger">Revision</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>

                                <td>{{ $item->catatan_spv ?? '-' }}</td>

                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#detailThumbling{{ $item->uuid }}">
                                        Lihat Detail
                                    </button>
                                </td>

                                <td class="text-center">
                                    {{ $item->created_at
                                        ? $item->created_at->format('d-m-Y H:i:s')
                                        : '-' }}
                                </td>
                            </tr>

                            <tr class="collapse" id="detailThumbling{{ $item->uuid }}">
                                <td colspan="32">
                                    <div class="row g-3 p-2">

                                        @php
                                            $detailFields = [
                                                'Kode Daging' => $item->kode_daging,
                                                'Berat Daging' => $item->berat_daging,
                                                'Suhu Daging' => $item->suhu_daging,
                                                'Rata-rata Daging' => $item->rata_daging,
                                                'Premix' => $item->premix,
                                                'Kode Premix' => $item->kode_premix,
                                                'Berat Premix' => $item->berat_premix,
                                                'Bahan Lain' => $item->bahan_lain,
                                                'Suhu Daging Thumbling' => $item->suhu_daging_thumbling,
                                            ];
                                        @endphp

                                        @foreach ($detailFields as $label => $values)
                                            <div class="col-md-6 col-xl-4">
                                                <div class="border rounded p-2 h-100">
                                                    <strong>{{ $label }}</strong>
                                                    <pre class="mb-0 mt-2 text-wrap">{{ json_encode($values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="32" class="text-center py-4">
                                    Belum ada data Audit Thumbling.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>

<style>
    .table th,
    .table td {
        white-space: nowrap;
        font-size: 0.85rem;
        vertical-align: middle;
    }

    pre {
        white-space: pre-wrap;
        word-break: break-word;
        font-size: 0.8rem;
    }
</style>
@endsection

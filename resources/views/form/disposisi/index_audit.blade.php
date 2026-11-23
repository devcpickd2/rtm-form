
@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <h4 class="mb-0">
                    <i class="bi bi-clipboard-check"></i>
                    Audit Disposisi Produk Tidak Sesuai
                </h4>

                <a href="{{ route('disposisi.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            {{-- Filter --}}
            <form method="GET"
                  action="{{ route('disposisi.audit') }}"
                  class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <input type="date"
                       name="date"
                       class="form-control"
                       style="max-width: 200px;"
                       value="{{ request('date') }}">

                <input type="text"
                       name="search"
                       class="form-control"
                       style="max-width: 300px;"
                       placeholder="Cari produk, kode produksi..."
                       value="{{ request('search') }}">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('disposisi.audit') }}" class="btn btn-secondary">
                    Reset
                </a>
            </form>

            {{-- Tabel Audit --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Jumlah</th>
                            <th>Ketidaksesuaian</th>
                            <th>Tindakan</th>
                            <th>Keterangan</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>Status Produksi</th>
                            <th>SPV</th>
                            <th>Status SPV</th>
                            <th>Catatan SPV</th>
                            <th>Waktu Audit</th>
                            <th>Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $i => $dep)
                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $i }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->date ? $dep->date->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->shift ?? '-' }}
                                </td>

                                <td>{{ $dep->nama_produk ?? '-' }}</td>

                                <td>{{ $dep->kode_produksi ?? '-' }}</td>

                                <td class="text-center">
                                    {{ $dep->jumlah ?? '-' }}
                                </td>

                                <td>{{ $dep->ketidaksesuaian ?? '-' }}</td>

                                <td>{{ $dep->tindakan ?? '-' }}</td>

                                <td>{{ $dep->keterangan ?? '-' }}</td>

                                <td>{{ $dep->catatan ?? '-' }}</td>

                                <td>{{ $dep->username ?? '-' }}</td>

                                <td>{{ $dep->nama_produksi ?? '-' }}</td>

                                <td class="text-center">
                                    @if ($dep->status_produksi == 1)
                                        <span class="badge bg-success">Checked</span>
                                    @elseif ($dep->status_produksi == 2)
                                        <span class="badge bg-danger">Revision</span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ $dep->status_produksi ?? 'Created' }}
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $dep->nama_spv ?? '-' }}</td>

                                <td class="text-center">
                                    @if ($dep->status_spv == 1)
                                        <span class="badge bg-success">Verified</span>
                                    @elseif ($dep->status_spv == 2)
                                        <span class="badge bg-danger">Revision</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>

                                <td>{{ $dep->catatan_spv ?? '-' }}</td>

                                <td class="text-center">
                                    {{ $dep->created_at
                                        ? $dep->created_at->format('d-m-Y H:i:s')
                                        : '-' }}
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-info text-white"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#detailAudit{{ $dep->uuid }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>

                            <tr class="collapse" id="detailAudit{{ $dep->uuid }}">
                                <td colspan="18">
                                    <div class="card card-body bg-light">
                                        <h6 class="fw-bold mb-3">
                                            Detail Audit Disposisi
                                        </h6>

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <strong>UUID Data Asli:</strong>
                                                <br>{{ $dep->source_uuid ?? '-' }}
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>UUID Audit:</strong>
                                                <br>{{ $dep->uuid }}
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Waktu Dibuat:</strong>
                                                <br>
                                                {{ $dep->created_at
                                                    ? $dep->created_at->format('d-m-Y H:i:s')
                                                    : '-' }}
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Terakhir Diubah oleh:</strong>
                                                <br>{{ $dep->username_updated ?? '-' }}
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Waktu Update Produksi:</strong>
                                                <br>{{ $dep->tgl_update_produksi ?? '-' }}
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Waktu Verifikasi SPV:</strong>
                                                <br>{{ $dep->tgl_update_spv ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="18" class="text-center">
                                    Belum ada data Audit Disposisi.
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
@endsection

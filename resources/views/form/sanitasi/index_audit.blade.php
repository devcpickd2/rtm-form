@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-1">Audit Pemantauan Sanitasi</h4>
                <small class="text-muted">
                    Data snapshot Sanitasi - hanya dapat dilihat
                </small>
            </div>

            <a href="{{ route('sanitasi.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>

        <form method="GET" action="{{ route('sanitasi.audit') }}"
            class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

            <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm"
                style="max-width: 180px;">

            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                placeholder="Cari QC, Produksi, keterangan..." style="max-width: 280px;">

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-search"></i> Cari
            </button>

            <a href="{{ route('sanitasi.audit') }}" class="btn btn-secondary btn-sm">
                Reset
            </a>
        </form>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Pukul</th>
                                <th>Standar Footbasin</th>
                                <th>Aktual Footbasin</th>
                                <th>Standar Handbasin</th>
                                <th>Aktual Handbasin</th>
                                <th>Keterangan</th>
                                <th>Tindakan Koreksi</th>
                                <th>Catatan</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>Status SPV</th>
                                <th>Catatan SPV</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $dep)
                                <tr>
                                    <td class="text-center">
                                        {{ $data->firstItem() + $loop->index }}
                                    </td>

                                    <td class="text-center text-nowrap">
                                        {{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->shift ?? '-' }}
                                    </td>

                                    <td class="text-center text-nowrap">
                                        {{ $dep->pukul ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->std_footbasin ?? '-' }} ppm
                                    </td>

                                    <td class="text-center">
                                        @if ($dep->aktual_footbasin)
                                            <a href="{{ asset('storage/' . $dep->aktual_footbasin) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $dep->aktual_footbasin) }}"
                                                    alt="Foto Aktual Footbasin"
                                                    style="width:90px;height:75px;object-fit:cover;" class="rounded border">
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->std_handbasin ?? '-' }} ppm
                                    </td>

                                    <td class="text-center">
                                        @if ($dep->aktual_handbasin)
                                            <a href="{{ asset('storage/' . $dep->aktual_handbasin) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $dep->aktual_handbasin) }}"
                                                    alt="Foto Aktual Handbasin"
                                                    style="width:90px;height:75px;object-fit:cover;" class="rounded border">
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>{{ $dep->keterangan ?? '-' }}</td>

                                    <td>{{ $dep->tindakan_koreksi ?? '-' }}</td>

                                    <td>{{ $dep->catatan ?? '-' }}</td>

                                    <td class="text-center">
                                        {{ $dep->username ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->nama_produksi ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        @if ($dep->status_spv == 1)
                                            <span class="badge bg-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <span class="badge bg-danger">Revision</span>
                                        @else
                                            <span class="badge bg-secondary">Created</span>
                                        @endif
                                    </td>

                                    <td>{{ $dep->catatan_spv ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-center py-4">
                                        Belum ada data Audit Sanitasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

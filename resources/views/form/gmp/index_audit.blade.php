@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">
                            <i class="bi bi-clipboard-check"></i>
                            Audit GMP Karyawan
                        </h4>
                        <small class="text-muted">
                            Riwayat pemeriksaan GMP - hanya dapat dilihat
                        </small>
                    </div>

                    <a href="{{ route('gmp.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <form method="GET" action="{{ route('gmp.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <input type="date" name="date" class="form-control form-control-sm" style="max-width:200px;"
                        value="{{ request('date') }}">

                    <input type="text" name="search" class="form-control form-control-sm" style="max-width:280px;"
                        placeholder="Cari QC, Produksi, atau SPV..." value="{{ request('search') }}">

                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-funnel"></i> Filter
                    </button>

                    <a href="{{ route('gmp.audit') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Noodle & Rice</th>
                                <th>Cooking</th>
                                <th>Packing</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>SPV</th>
                                <th>Status SPV</th>
                                <th>Detail Audit</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $dep)
                                @php
                                    $areaGmp = [
                                        'Noodle & Rice' => $dep->noodle_rice ?? [],
                                        'Cooking' => $dep->cooking ?? [],
                                        'Packing' => $dep->packing ?? [],
                                    ];

                                    $hitungPersentaseAudit = function ($rows) {
                                        $total = 0;
                                        $sesuai = 0;

                                        foreach ($rows as $row) {
                                            foreach (['seragam', 'boot', 'masker', 'ciput', 'parfum'] as $atribut) {
                                                $total++;
                                                if (($row[$atribut] ?? 0) == 1) {
                                                    $sesuai++;
                                                }
                                            }
                                        }

                                        return $total > 0 ? round(($sesuai / $total) * 100, 1) : 0;
                                    };
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        {{ $data->firstItem() + $loop->index }}
                                    </td>

                                    <td class="text-center text-nowrap">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                    </td>

                                    <td class="text-center">
                                        {{ $hitungPersentaseAudit($areaGmp['Noodle & Rice']) }}%
                                    </td>

                                    <td class="text-center">
                                        {{ $hitungPersentaseAudit($areaGmp['Cooking']) }}%
                                    </td>

                                    <td class="text-center">
                                        {{ $hitungPersentaseAudit($areaGmp['Packing']) }}%
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->username ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->nama_produksi ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->nama_spv ?? '-' }}
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

                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#auditGmp{{ $dep->uuid }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Detail Audit --}}
                                <div class="modal fade" id="auditGmp{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">

                                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                        <div class="modal-content">

                                            <div class="modal-header bg-primary text-white">
                                                <div>
                                                    <h5 class="modal-title fw-bold">
                                                        Detail Audit GMP Karyawan
                                                    </h5>
                                                    <small>
                                                        Tanggal:
                                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                                    </small>
                                                </div>

                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal">
                                                </button>
                                            </div>

                                            <div class="modal-body">

                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <strong>QC:</strong>
                                                        {{ $dep->username ?? '-' }}
                                                    </div>

                                                    <div class="col-md-4">
                                                        <strong>Produksi:</strong>
                                                        {{ $dep->nama_produksi ?? '-' }}
                                                    </div>

                                                    <div class="col-md-4">
                                                        <strong>SPV:</strong>
                                                        {{ $dep->nama_spv ?? '-' }}
                                                    </div>
                                                </div>

                                                @foreach ($areaGmp as $namaArea => $rows)
                                                    <div class="mb-4">
                                                        <h6 class="fw-bold bg-light border p-2">
                                                            {{ $namaArea }}
                                                        </h6>

                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered align-middle">
                                                                <thead class="table-secondary text-center">
                                                                    <tr>
                                                                        <th>No.</th>
                                                                        <th>Nama Karyawan</th>
                                                                        <th>Seragam</th>
                                                                        <th>Boot</th>
                                                                        <th>Masker</th>
                                                                        <th>Ciput</th>
                                                                        <th>Parfum</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    @forelse ($rows as $karyawan)
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                {{ $loop->iteration }}
                                                                            </td>

                                                                            <td>
                                                                                {{ $karyawan['nama_karyawan'] ?? '-' }}
                                                                            </td>

                                                                            @foreach (['seragam', 'boot', 'masker', 'ciput', 'parfum'] as $atribut)
                                                                                <td class="text-center">
                                                                                    @if (($karyawan[$atribut] ?? 0) == 1)
                                                                                        <span
                                                                                            class="badge bg-success">Sesuai</span>
                                                                                    @else
                                                                                        <span class="badge bg-danger">Tidak
                                                                                            Sesuai</span>
                                                                                    @endif
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="7" class="text-center">
                                                                                Tidak ada data karyawan.
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <div class="border rounded p-3">
                                                    <h6 class="fw-bold">Informasi Verifikasi</h6>

                                                    <div class="row">
                                                        <div class="col-md-4 mb-2">
                                                            <strong>Status SPV:</strong>
                                                            @if ($dep->status_spv == 1)
                                                                <span class="badge bg-success">Verified</span>
                                                            @elseif ($dep->status_spv == 2)
                                                                <span class="badge bg-danger">Revision</span>
                                                            @else
                                                                <span class="badge bg-secondary">Created</span>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-8 mb-2">
                                                            <strong>Catatan SPV:</strong>
                                                            {{ $dep->catatan_spv ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Tutup
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            Belum ada data Audit GMP.
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

        <style>
            .table td,
            .table th {
                font-size: 0.85rem;
                white-space: nowrap;
            }

            .modal .table td,
            .modal .table th {
                font-size: 0.8rem;
            }
        </style>
    @endsection

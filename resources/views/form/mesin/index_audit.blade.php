
@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <h4 class="mb-0">
                    <i class="bi bi-clipboard-check"></i>
                    Audit Verifikasi Mesin
                </h4>

                <a href="{{ route('mesin.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <form method="GET"
                  action="{{ route('mesin.audit') }}"
                  class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <input type="date"
                       name="date"
                       class="form-control"
                       style="max-width:220px"
                       value="{{ request('date') }}">

                <input type="text"
                       name="search"
                       class="form-control"
                       style="max-width:300px"
                       placeholder="Cari username, mesin, tindakan..."
                       value="{{ request('search') }}">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('mesin.audit') }}" class="btn btn-secondary">
                    Reset
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Nama Mesin</th>
                            <th>Standar Setting</th>
                            <th>Aktual</th>
                            <th>Tindakan Perbaikan</th>
                            <th>Keterangan</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Status SPV</th>
                            <th>Waktu Audit</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $i => $row)
                            @php
                                $detailMesin = $row->verif_mesin;

                                if (is_string($detailMesin)) {
                                    $detailMesin = json_decode($detailMesin, true) ?? [];
                                }

                                if (!is_array($detailMesin)) {
                                    $detailMesin = [];
                                }
                            @endphp

                            @if (count($detailMesin))
                                @foreach ($detailMesin as $detail)
                                    <tr>
                                        @if ($loop->first)
                                            <td rowspan="{{ count($detailMesin) }}" class="text-center">
                                                {{ $data->firstItem() + $i }}
                                            </td>

                                            <td rowspan="{{ count($detailMesin) }}" class="text-center">
                                                {{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '-' }}
                                            </td>

                                            <td rowspan="{{ count($detailMesin) }}" class="text-center">
                                                {{ $row->shift ?? '-' }}
                                            </td>
                                        @endif

                                        <td>{{ $detail['nama_mesin'] ?? '-' }}</td>
                                        <td class="text-center">{{ $detail['standar_setting'] ?? '-' }}</td>
                                        <td class="text-center">{{ $detail['aktual'] ?? '-' }}</td>

                                        @if ($loop->first)
                                            <td rowspan="{{ count($detailMesin) }}">
                                                {{ $row->tindakan_perbaikan ?? '-' }}
                                            </td>

                                            <td rowspan="{{ count($detailMesin) }}">
                                                {{ $row->keterangan ?? '-' }}
                                            </td>

                                            <td rowspan="{{ count($detailMesin) }}">
                                                {{ $row->catatan ?? '-' }}
                                            </td>

                                            <td rowspan="{{ count($detailMesin) }}">
                                                {{ $row->username ?? '-' }}
                                            </td>

                                            <td rowspan="{{ count($detailMesin) }}">
                                                {{ $row->nama_produksi ?? '-' }}
                                            </td>

                                            <td rowspan="{{ count($detailMesin) }}">
                                                {{ $row->nama_spv ?? '-' }}
                                            </td>

                                            <td rowspan="{{ count($detailMesin) }}" class="text-center">
                                                @if ((string) $row->status_spv === '1')
                                                    <span class="badge bg-success">Verified</span>
                                                @elseif ((string) $row->status_spv === '2')
                                                    <span class="badge bg-danger">Revision</span>
                                                @else
                                                    <span class="badge bg-secondary">Created</span>
                                                @endif
                                            </td>

                                            <td rowspan="{{ count($detailMesin) }}" class="text-center">
                                                {{ $row->created_at
                                                    ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i')
                                                    : '-' }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center">{{ $data->firstItem() + $i }}</td>
                                    <td class="text-center">
                                        {{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '-' }}
                                    </td>
                                    <td class="text-center">{{ $row->shift ?? '-' }}</td>
                                    <td colspan="3" class="text-center">Tidak ada detail mesin</td>
                                    <td>{{ $row->tindakan_perbaikan ?? '-' }}</td>
                                    <td>{{ $row->keterangan ?? '-' }}</td>
                                    <td>{{ $row->catatan ?? '-' }}</td>
                                    <td>{{ $row->username ?? '-' }}</td>
                                    <td>{{ $row->nama_produksi ?? '-' }}</td>
                                    <td>{{ $row->nama_spv ?? '-' }}</td>
                                    <td class="text-center">{{ $row->status_spv ?? '-' }}</td>
                                    <td class="text-center">
                                        {{ $row->created_at ? $row->created_at->format('d-m-Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="14" class="text-center py-4">
                                    Belum ada data Audit Verifikasi Mesin.
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

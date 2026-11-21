@extends('layouts.app')

@section('content')
    @php
        function auditCheckIconKebersihan($arr)
        {
            return !empty($arr['jam'])
                ? '<i class="bi bi-check-circle-fill text-success"></i>'
                : '<i class="bi bi-x-circle text-danger"></i>';
        }
    @endphp

    <div class="container-fluid py-0">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="mb-1">
                            <i class="bi bi-clipboard-check"></i>
                            Audit Kebersihan Ruang, Mesin dan Peralatan Produksi
                        </h3>
                        <small class="text-muted">
                            Data riwayat Audit, hanya dapat dilihat
                        </small>
                    </div>

                    <a href="{{ route('kebersihan_ruang.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <form method="GET" action="{{ route('kebersihan_ruang.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <div class="input-group" style="max-width:220px;">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-calendar-date"></i>
                        </span>

                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>

                    <input type="text" name="search" class="form-control" style="max-width:280px;"
                        placeholder="Cari QC, Produksi, SPV, catatan..." value="{{ request('search') }}">

                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>

                    <a href="{{ route('kebersihan_ruang.audit') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Rice - Boiling</th>
                                <th>Noodle</th>
                                <th>Chillroom</th>
                                <th>CS 1</th>
                                <th>CS 2</th>
                                <th>Seasoning</th>
                                <th>Prep Room</th>
                                <th>Cooking</th>
                                <th>Filling</th>
                                <th>Topping</th>
                                <th>Packing</th>
                                <th>IQF</th>
                                <th>CS FG</th>
                                <th>DryStore</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>Status Produksi</th>
                                <th>SPV</th>
                                <th>Status SPV</th>
                                <th>Catatan SPV</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $dep)
                                <tr>
                                    <td class="text-center">
                                        {{ $data->firstItem() + $loop->index }}
                                    </td>

                                    <td class="text-center text-nowrap">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                        | Shift: {{ $dep->shift }}
                                    </td>

                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->rice_boiling ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->noodle ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->cr_rm ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->cs_1 ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->cs_2 ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->seasoning ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->prep_room ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->cooking ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->filling ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->topping ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->packing ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->iqf ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->cs_fg ?? []) !!}</td>
                                    <td class="text-center">{!! auditCheckIconKebersihan($dep->ds ?? []) !!}</td>

                                    <td class="text-center">{{ $dep->username ?? '-' }}</td>
                                    <td class="text-center">{{ $dep->nama_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        @if ($dep->status_produksi == 0)
                                            <span class="badge bg-secondary">Created</span>
                                        @elseif ($dep->status_produksi == 1)
                                            <span class="badge bg-success">Checked</span>
                                        @elseif ($dep->status_produksi == 2)
                                            <span class="badge bg-warning text-dark">Revision</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center">{{ $dep->nama_spv ?? '-' }}</td>

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
                                    <td>{{ $dep->catatan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="23" class="text-center py-4">
                                        Belum ada data Audit Kebersihan Ruang.
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

        .table thead th {
            background-color: #cfe2ff !important;
            color: #000 !important;
        }
    </style>
@endsection


@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h3 class="mb-1">
                        <i class="bi bi-clipboard-check"></i>
                        Audit Memo Retur
                    </h3>
                    <small class="text-muted">
                        Snapshot Memo Retur saat pertama kali dibuat.
                    </small>
                </div>

                @if ((int) auth()->user()->type_user !== 5)
                    <a href="{{ route('memo.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('memo.audit') }}"
                class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <div class="input-group" style="max-width:220px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-calendar-date"></i>
                    </span>
                    <input type="date" name="date" class="form-control"
                        value="{{ request('date') }}">
                </div>

                <div class="input-group" style="max-width:350px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari From, To, Subject, Detail..."
                        value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('memo.audit') }}" class="btn btn-outline-secondary">
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
                            <th>From</th>
                            <th>To</th>
                            <th>Subject</th>
                            <th>Detail Memo Retur</th>
                            <th>QC</th>
                            <th>Terakhir Diubah Oleh</th>
                            <th>SPV</th>
                            <th>Status SPV</th>
                            <th>Catatan SPV</th>
                            <th>Waktu Verifikasi SPV</th>
                            <th>Waktu Snapshot</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $item->date ? $item->date->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">{{ $item->shift ?? '-' }}</td>
                                <td>{{ $item->retur_dari ?? '-' }}</td>
                                <td>{{ $item->retur_ke ?? '-' }}</td>
                                <td>{{ $item->subject ?? '-' }}</td>

                                <td style="min-width:350px; white-space:pre-wrap;">
                                    {{ $item->detail_retur ?? '-' }}
                                </td>

                                <td>{{ $item->username ?? '-' }}</td>
                                <td>{{ $item->username_updated ?? '-' }}</td>
                                <td>{{ $item->nama_spv ?? '-' }}</td>

                                <td class="text-center">
                                    @if ((string) $item->status_spv === '1')
                                        <span class="badge bg-success">Verified</span>
                                    @elseif ((string) $item->status_spv === '2')
                                        <span class="badge bg-danger">Revision</span>
                                    @elseif ((string) $item->status_spv === '0')
                                        <span class="badge bg-warning text-dark">Created</span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ $item->status_spv ?? '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td style="min-width:180px; white-space:pre-wrap;">
                                    {{ $item->catatan_spv ?? '-' }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $item->tgl_update_spv ? \Carbon\Carbon::parse($item->tgl_update_spv)->format('d-m-Y H:i:s') : '-' }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center py-4">
                                    Belum ada data Audit Memo Retur.
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

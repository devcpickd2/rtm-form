
@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h3 class="mb-1">
                        <i class="bi bi-clipboard-check"></i>
                        Audit Retained Sample Report
                    </h3>
                    <small class="text-muted">
                        Data snapshot Retain saat pertama kali disimpan.
                    </small>
                </div>

                @if ((int) auth()->user()->type_user !== 5)
                    <a href="{{ route('retain.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                @endif
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="GET" action="{{ route('retain.audit') }}"
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
                        value="{{ request('search') }}"
                        placeholder="Cari plant, produk, kode produksi...">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('retain.audit') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Collection Date</th>
                            <th>Plant</th>
                            <th>Sample Type</th>
                            <th>Sample Storage</th>
                            <th>Description</th>
                            <th>Production Code</th>
                            <th>Best Before</th>
                            <th>Quantity (gr)</th>
                            <th>Remarks</th>
                            <th>Note</th>
                            <th>Warehouse</th>
                            <th>Status Warehouse</th>
                            <th>Nama SPV</th>
                            <th>Status SPV</th>
                            <th>Catatan SPV</th>
                            <th>Diinput Oleh</th>
                            <th>Waktu Snapshot</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $item)
                            @php
                                $storage = $item->sample_storage;

                                if (is_string($storage)) {
                                    $storage = json_decode($storage, true);
                                }

                                if (!is_array($storage)) {
                                    $storage = [];
                                }

                                $statusWarehouse = (string) $item->status_warehouse;
                                $statusSpv = (string) $item->status_spv;
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>
                                <td>{{ optional($item->date)->format('d-m-Y') ?? $item->date }}</td>
                                <td>{{ $item->plant ?? '-' }}</td>
                                <td>{{ $item->sample_type ?? '-' }}</td>
                                <td>{{ count($storage) ? implode(', ', $storage) : '-' }}</td>
                                <td>{{ $item->description ?? '-' }}</td>
                                <td>{{ $item->production_code ?? '-' }}</td>
                                <td>{{ optional($item->best_before)->format('d-m-Y') ?? $item->best_before }}</td>
                                <td class="text-end">{{ $item->quantity ?? '-' }}</td>
                                <td>{{ $item->remarks ?? '-' }}</td>
                                <td>{{ $item->note ?? '-' }}</td>
                                <td>{{ $item->nama_warehouse ?? '-' }}</td>

                                <td class="text-center">
                                    @if ($statusWarehouse === '1')
                                        <span class="badge bg-success">Submitted</span>
                                    @elseif ($statusWarehouse === '2')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $statusWarehouse ?: '-' }}</span>
                                    @endif
                                </td>

                                <td>{{ $item->nama_spv ?? '-' }}</td>

                                <td class="text-center">
                                    @if ($statusSpv === '1')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif ($statusSpv === '2')
                                        <span class="badge bg-danger">Rejected</span>
                                    @elseif ($statusSpv === '0')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $statusSpv ?: '-' }}</span>
                                    @endif
                                </td>

                                <td>{{ $item->catatan_spv ?? '-' }}</td>
                                <td>{{ $item->username ?? '-' }}</td>
                                <td>{{ optional($item->created_at)->format('d-m-Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="18" class="text-center py-4">
                                    Belum ada data Audit Retain.
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

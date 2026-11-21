@extends('layouts.app')

@section('content')
    <div class="container-fluid py-2">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <h3 class="mb-0">
                        <i class="bi bi-shield-check"></i>
                        Audit Verifikasi Premix
                    </h3>

                    <a href="{{ route('premix.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <form method="GET" action="{{ route('premix.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                    <input type="date" name="date" class="form-control" style="max-width:220px"
                        value="{{ request('date') }}">

                    <input type="text" name="search" class="form-control" style="max-width:350px"
                        placeholder="Cari nama premix, kode produksi, sensori..." value="{{ request('search') }}">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>

                    <a href="{{ route('premix.audit') }}" class="btn btn-secondary">
                        Reset
                    </a>
                </form>

                <div class="alert alert-info">
                    Data pada halaman ini merupakan salinan kondisi data Premix saat pertama kali disimpan.
                    Data Audit hanya dapat dilihat dan tidak dapat diubah.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Nama Premix</th>
                                <th>Kode Produksi</th>
                                <th>Sensori</th>
                                <th>Tindakan Koreksi</th>
                                <th>Catatan</th>
                                <th>Produksi</th>
                                <th>Status Produksi</th>
                                <th>SPV</th>
                                <th>Status SPV</th>
                                <th>Catatan SPV</th>
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
                                        {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d-m-Y') : '-' }}
                                    </td>

                                    <td class="text-center">{{ $item->shift ?? '-' }}</td>
                                    <td>{{ $item->nama_premix ?? '-' }}</td>
                                    <td>{{ $item->kode_produksi ?? '-' }}</td>
                                    <td>{{ $item->sensori ?? '-' }}</td>
                                    <td>{{ $item->tindakan_koreksi ?? '-' }}</td>
                                    <td>{{ $item->catatan ?? '-' }}</td>
                                    <td>{{ $item->nama_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        @if ($item->status_produksi == 1)
                                            <span class="badge bg-success">Submitted</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $item->status_produksi ?? '-' }}</span>
                                        @endif
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
                                        {{ $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center py-4">
                                        Belum ada data Audit Premix.
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

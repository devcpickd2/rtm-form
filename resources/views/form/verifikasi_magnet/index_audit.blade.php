@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div>
                    <h3 class="mb-1">
                        <i class="bi bi-archive"></i>
                        Audit Verifikasi Magnet Trap
                    </h3>
                    <small class="text-muted">
                        Data snapshot saat pertama kali disimpan
                    </small>
                </div>

                <a href="{{ route('verifikasi_magnet.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>
            </div>

            {{-- Filter --}}
            <form method="GET"
                  action="{{ route('verifikasi_magnet.audit') }}"
                  class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <div class="input-group" style="max-width:220px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-calendar-date"></i>
                    </span>

                    <input type="date"
                           name="date"
                           class="form-control"
                           value="{{ request('date') }}">
                </div>

                <div class="input-group flex-grow-1" style="max-width:350px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>

                    <input type="text"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Cari username, produk, kode produksi...">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                @if(request('search') || request('date'))
                    <a href="{{ route('verifikasi_magnet.audit') }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>
                @endif

            </form>

            {{-- Tabel Audit --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Pukul</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Bukti</th>
                            <th>Jumlah Temuan</th>
                            <th>Keterangan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>Status SPV</th>
                            <th>Catatan SPV</th>
                            <th>Waktu Snapshot</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $dep)
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

                                <td class="text-center">
                                    {{ $dep->pukul ? \Carbon\Carbon::parse($dep->pukul)->format('H:i') : '-' }}
                                </td>

                                <td>{{ $dep->nama_produk ?? '-' }}</td>

                                <td>{{ $dep->kode_produksi ?? '-' }}</td>

                                <td class="text-center">
                                    @if($dep->bukti)
                                        <a href="{{ asset('storage/' . $dep->bukti) }}"
                                           target="_blank"
                                           rel="noopener noreferrer">
                                            <img src="{{ asset('storage/' . $dep->bukti) }}"
                                                 alt="Bukti Verifikasi Magnet"
                                                 loading="lazy"
                                                 style="max-width:100px; height:auto;">
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{ $dep->jumlah_temuan ?? '-' }}
                                </td>

                                <td>{{ $dep->keterangan ?? '-' }}</td>

                                <td>{{ $dep->username ?? '-' }}</td>

                                <td>{{ $dep->nama_produksi ?? '-' }}</td>

                                <td class="text-center">
                                    @if($dep->status_spv == 1)
                                        <span class="badge bg-success">Verified</span>
                                    @elseif($dep->status_spv == 2)
                                        <span class="badge bg-danger">Revision</span>
                                    @elseif($dep->status_spv == 0)
                                        <span class="badge bg-secondary">Created</span>
                                    @else
                                        {{ $dep->status_spv ?? '-' }}
                                    @endif
                                </td>

                                <td>{{ $dep->catatan_spv ?? '-' }}</td>

                                <td class="text-center text-nowrap">
                                    {{ $dep->created_at
                                        ? \Carbon\Carbon::parse($dep->created_at)->format('d-m-Y H:i:s')
                                        : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center py-4">
                                    <i class="bi bi-inbox text-muted"
                                       style="font-size:1.5rem;"></i>
                                    <br>
                                    Belum ada data Audit Verifikasi Magnet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data->total() > 0)
                <div class="text-muted small mb-2">
                    Menampilkan {{ $data->firstItem() }}-{{ $data->lastItem() }}
                    dari {{ $data->total() }} data Audit
                </div>
            @endif

            <div class="mt-3">
                {{ $data->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>
@endsection

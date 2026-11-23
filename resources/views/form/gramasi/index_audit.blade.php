
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h3>
                        <i class="bi bi-clock-history"></i>
                        Audit Verifikasi Gramasi Topping
                    </h3>
                    <small class="text-muted">
                        Data snapshot pemeriksaan Gramasi yang tersimpan saat input pertama kali
                    </small>
                </div>

                @if ((int) auth()->user()->type_user !== 5)
                    <a href="{{ route('gramasi.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                @endif
            </div>

            {{-- Filter --}}
            <form method="GET" action="{{ route('gramasi.audit') }}"
                class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <div class="input-group" style="max-width:220px">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-calendar-date"></i>
                    </span>
                    <input type="date" name="date" class="form-control"
                        value="{{ request('date') }}">
                </div>

                <div class="input-group" style="max-width:350px">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Cari produk / kode produksi / username">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('gramasi.audit') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </form>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Halaman ini hanya menampilkan data Audit dan tidak menyediakan fitur
                tambah, edit, hapus, verifikasi, maupun cetak PDF.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal / Shift</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Gramasi Topping</th>
                            <th>Tindakan Koreksi</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Waktu Snapshot</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $index => $row)
                            @php
                                $items = $row->gramasi_topping;

                                if (is_string($items)) {
                                    $decoded = json_decode($items, true);
                                    $items = is_array($decoded) ? $decoded : [];
                                } elseif (!is_array($items)) {
                                    $items = [];
                                }
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $index }}
                                </td>

                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}
                                    <br>
                                    Shift {{ $row->shift }}
                                </td>

                                <td>{{ $row->nama_produk ?? '-' }}</td>
                                <td>{{ $row->kode_produksi ?? '-' }}</td>

                                <td style="min-width:420px">
                                    @if (count($items))
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="table-light text-center">
                                                    <tr>
                                                        <th>Jenis Topping</th>
                                                        <th>Standar</th>
                                                        <th>Pukul 1</th>
                                                        <th>Gramasi 1</th>
                                                        <th>Pukul 2</th>
                                                        <th>Gramasi 2</th>
                                                        <th>Pukul 3</th>
                                                        <th>Gramasi 3</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($items as $item)
                                                        <tr>
                                                            <td>{{ $item['jenis_topping'] ?? '-' }}</td>
                                                            <td class="text-center">{{ $item['standar'] ?? '-' }}</td>
                                                            <td class="text-center">{{ $item['pukul_1'] ?? '-' }}</td>
                                                            <td class="text-center">{{ $item['gramasi_1'] ?? '-' }}</td>
                                                            <td class="text-center">{{ $item['pukul_2'] ?? '-' }}</td>
                                                            <td class="text-center">{{ $item['gramasi_2'] ?? '-' }}</td>
                                                            <td class="text-center">{{ $item['pukul_3'] ?? '-' }}</td>
                                                            <td class="text-center">{{ $item['gramasi_3'] ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <span class="text-muted">Tidak ada detail gramasi</span>
                                    @endif
                                </td>

                                <td>{{ $row->tindakan_koreksi ?? '-' }}</td>
                                <td>{{ $row->catatan ?? '-' }}</td>
                                <td>{{ $row->username ?? '-' }}</td>
                                <td>{{ $row->nama_produksi ?? '-' }}</td>

                                <td>
                                    {{ $row->nama_spv ?? '-' }}
                                    <br>
                                    @if ($row->status_spv == 1)
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif ($row->status_spv == 2)
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">Belum diverifikasi</span>
                                    @endif

                                    @if (!empty($row->catatan_spv))
                                        <div class="small text-muted mt-1">
                                            Catatan: {{ $row->catatan_spv }}
                                        </div>
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{ $row->created_at
                                        ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i:s')
                                        : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    Belum ada data Audit Gramasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $data->links() }}
            </div>

        </div>
    </div>
</div>
@endsection

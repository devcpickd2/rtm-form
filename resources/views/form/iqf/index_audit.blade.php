
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h3>
                        <i class="bi bi-clock-history"></i>
                        Audit Pemeriksaan Suhu Produk Setelah IQF
                    </h3>
                    <small class="text-muted">
                        Riwayat snapshot data pemeriksaan IQF
                    </small>
                </div>

                @if ((int) auth()->user()->type_user !== 5)
                    <a href="{{ route('iqf.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                @endif
            </div>

            {{-- Filter --}}
            <form method="GET" action="{{ route('iqf.audit') }}"
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
                        placeholder="Cari produk, kode produksi, IQF No.">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('iqf.audit') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </form>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Halaman Audit hanya dapat digunakan untuk melihat data.
                Data pada halaman ini tidak dapat diedit, dihapus, diverifikasi,
                maupun dicetak melalui PDF.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>IQF No.</th>
                            <th>Pukul</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Standar Suhu</th>
                            <th>Suhu Pusat Produk</th>
                            <th>Average</th>
                            <th>Problem</th>
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
                                $suhu = $row->suhu_pusat;

                                if (is_string($suhu)) {
                                    $decoded = json_decode($suhu, true);
                                    $suhu = is_array($decoded) ? $decoded : [];
                                }

                                if (!is_array($suhu)) {
                                    $suhu = [];
                                }
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $index }}
                                </td>

                                <td class="text-center">
                                    {{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">{{ $row->shift ?? '-' }}</td>
                                <td>{{ $row->no_iqf ?? '-' }}</td>
                                <td class="text-center">{{ $row->pukul ?? '-' }}</td>
                                <td>{{ $row->nama_produk ?? '-' }}</td>
                                <td>{{ $row->kode_produksi ?? '-' }}</td>
                                <td class="text-center">{{ $row->std_suhu ?? '-' }}</td>

                                <td style="min-width:350px">
                                    <table class="table table-sm table-bordered mb-0 text-center">
                                        <thead class="table-light">
                                            <tr>
                                                @for ($i = 1; $i <= 10; $i++)
                                                    <th>{{ $i }}</th>
                                                @endfor
                                                <th>X</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                @for ($i = 0; $i < 10; $i++)
                                                    <td>
                                                        {{ $suhu[$i]['value'] ?? '-' }}
                                                    </td>
                                                @endfor
                                                <td>{{ $row->average ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                @for ($i = 0; $i < 10; $i++)
                                                    <td>
                                                        {{ $suhu[$i]['ket'] ?? '-' }}
                                                    </td>
                                                @endfor
                                                <td>-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>

                                <td class="text-center">{{ $row->average ?? '-' }}</td>
                                <td>{{ $row->problem ?? '-' }}</td>
                                <td>{{ $row->tindakan_koreksi ?? '-' }}</td>
                                <td>{{ $row->catatan ?? '-' }}</td>
                                <td>{{ $row->username ?? '-' }}</td>
                                <td>{{ $row->nama_produksi ?? '-' }}</td>

                                <td>
                                    <div>{{ $row->nama_spv ?? '-' }}</div>

                                    @if ($row->status_spv == 1)
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif ($row->status_spv == 2)
                                        <span class="badge bg-danger">Perlu Perbaikan</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Verifikasi</span>
                                    @endif

                                    @if (!empty($row->catatan_spv))
                                        <div class="small text-muted mt-1">
                                            {{ $row->catatan_spv }}
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
                                <td colspan="17" class="text-center text-muted py-4">
                                    Belum ada data Audit IQF.
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

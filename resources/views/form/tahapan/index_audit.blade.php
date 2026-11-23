
@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <h4 class="mb-0">
                    <i class="bi bi-clipboard-check"></i>
                    Audit Pengecekan Suhu Produk Setiap Tahapan Proses
                </h4>

                <a href="{{ route('tahapan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <form method="GET"
                  action="{{ route('tahapan.audit') }}"
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
                       placeholder="Cari produk, kode produksi, username..."
                       value="{{ request('search') }}">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('tahapan.audit') }}" class="btn btn-secondary">
                    Reset
                </a>
            </form>

            @php
                $suhuFields = [
                    'suhu_masuk_iqf' => 'Suhu Masuk IQF',
                    'suhu_keluar_iqf' => 'Suhu Keluar IQF',
                    'suhu_sealer' => 'Suhu Sealer',
                    'suhu_xray' => 'Suhu X-Ray',
                    'suhu_sticker' => 'Suhu Sticker',
                    'suhu_shrink' => 'Suhu Shrink',
                ];

                $tanggalFields = [
                    'tanggal_filling' => 'Tanggal Filling',
                    'tanggal_iqf' => 'Tanggal IQF',
                    'tanggal_sealer' => 'Tanggal Sealer',
                    'tanggal_xray' => 'Tanggal X-Ray',
                    'tanggal_sticker' => 'Tanggal Sticker',
                    'tanggal_shrink' => 'Tanggal Shrink',
                    'tanggal_packing' => 'Tanggal Packing',
                    'tanggal_cs' => 'Tanggal Cold Storage',
                ];

                $waktuFields = [
                    'filling_mulai' => 'Filling Mulai',
                    'filling_selesai' => 'Filling Selesai',
                    'waktu_iqf' => 'Waktu IQF',
                    'waktu_sealer' => 'Waktu Sealer',
                    'waktu_xray' => 'Waktu X-Ray',
                    'waktu_sticker' => 'Waktu Sticker',
                    'waktu_shrink' => 'Waktu Shrink',
                    'waktu_packing' => 'Waktu Packing',
                    'waktu_cs' => 'Waktu Cold Storage',
                ];

                $formatJson = function ($value) {
                    if (is_string($value)) {
                        $decoded = json_decode($value, true);

                        if (json_last_error() === JSON_ERROR_NONE) {
                            $value = $decoded;
                        }
                    }

                    if (is_array($value)) {
                        return count($value)
                            ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                            : '-';
                    }

                    return ($value !== null && $value !== '') ? $value : '-';
                };
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Downtime</th>
                            <th>Suhu Cold Storage</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Status SPV</th>
                            <th>Detail Tahapan</th>
                            <th>Waktu Audit</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $i => $row)
                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $i }}
                                </td>

                                <td class="text-center">
                                    {{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">{{ $row->shift ?? '-' }}</td>
                                <td>{{ $row->nama_produk ?? '-' }}</td>
                                <td>{{ $row->kode_produksi ?? '-' }}</td>
                                <td class="text-center">{{ $row->downtime ?? '-' }}</td>
                                <td class="text-center">{{ $row->suhu_cs ?? '-' }}</td>
                                <td>{{ $row->catatan ?? '-' }}</td>
                                <td>{{ $row->username ?? '-' }}</td>
                                <td>{{ $row->nama_produksi ?? '-' }}</td>
                                <td>{{ $row->nama_spv ?? '-' }}</td>

                                <td class="text-center">
                                    @if ((string) $row->status_spv === '1')
                                        <span class="badge bg-success">Verified</span>
                                    @elseif ((string) $row->status_spv === '2')
                                        <span class="badge bg-danger">Revision</span>
                                    @else
                                        <span class="badge bg-secondary">Created</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-info text-white"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#detailTahapan{{ $row->uuid }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>

                                <td class="text-center">
                                    {{ $row->created_at
                                        ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i')
                                        : '-' }}
                                </td>
                            </tr>

                            <tr class="collapse" id="detailTahapan{{ $row->uuid }}">
                                <td colspan="14">
                                    <div class="p-3 bg-light rounded">

                                        <h6 class="fw-bold mb-3">
                                            Detail Pemeriksaan Suhu Tahapan
                                        </h6>

                                        <div class="table-responsive mb-3">
                                            <table class="table table-sm table-bordered bg-white">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th>Waktu Proses</th>
                                                        <th>Tanggal Proses</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($waktuFields as $field => $label)
                                                        <tr>
                                                            <td>{{ $label }}</td>
                                                            <td>
                                                                {{ $row->$field
                                                                    ? \Carbon\Carbon::parse($row->$field)->format('H:i')
                                                                    : '-' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                    @foreach ($tanggalFields as $field => $label)
                                                        <tr>
                                                            <td>{{ $label }}</td>
                                                            <td>
                                                                {{ $row->$field
                                                                    ? \Carbon\Carbon::parse($row->$field)->format('d-m-Y')
                                                                    : '-' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <h6 class="fw-bold mb-2">Suhu Filling</h6>

                                        @php
                                            $filling = $row->suhu_filling;

                                            if (is_string($filling)) {
                                                $filling = json_decode($filling, true) ?? [];
                                            }

                                            if (!is_array($filling)) {
                                                $filling = [];
                                            }
                                        @endphp

                                        <div class="table-responsive mb-3">
                                            <table class="table table-sm table-bordered bg-white">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th>Nama Bahan</th>
                                                        <th>Suhu (°C)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($filling as $item)
                                                        <tr>
                                                            <td>{{ $item['nama_bahan'] ?? '-' }}</td>
                                                            <td>{{ $item['suhu'] ?? '-' }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="2" class="text-center">Tidak ada data suhu filling.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <h6 class="fw-bold mb-2">Suhu Tahapan Lainnya</h6>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered bg-white">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th>Tahapan</th>
                                                        <th>Data Suhu</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($suhuFields as $field => $label)
                                                        <tr>
                                                            <td>{{ $label }}</td>
                                                            <td>
                                                                <pre class="mb-0 text-wrap">{{ $formatJson($row->$field) }}</pre>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        @if ($row->keterangan)
                                            <div class="mt-3">
                                                <strong>Keterangan:</strong>
                                                <p class="mb-0">{{ $row->keterangan }}</p>
                                            </div>
                                        @endif

                                        @if ($row->catatan_spv)
                                            <div class="mt-3">
                                                <strong>Catatan SPV:</strong>
                                                <p class="mb-0">{{ $row->catatan_spv }}</p>
                                            </div>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center py-4">
                                    Belum ada data Audit Tahapan.
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

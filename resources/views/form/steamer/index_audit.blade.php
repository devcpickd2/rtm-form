@extends('layouts.app')

@section('content')
    <div class="container-fluid py-2">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <h3 class="mb-0">
                        <i class="bi bi-shield-check"></i>
                        Audit Pemeriksaan Pemasakan dengan Steamer
                    </h3>

                    <a href="{{ route('steamer.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <form method="GET" action="{{ route('steamer.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                    <input type="date" name="date" class="form-control" style="max-width:220px"
                        value="{{ request('date') }}">

                    <input type="text" name="search" class="form-control" style="max-width:350px"
                        placeholder="Cari produk, catatan, atau username..." value="{{ request('search') }}">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>

                    <a href="{{ route('steamer.audit') }}" class="btn btn-secondary">
                        Reset
                    </a>
                </form>

                <div class="alert alert-info">
                    Data Audit merupakan salinan kondisi data Steamer saat pertama kali disimpan.
                    Seluruh detail pemeriksaan dan catatan tersimpan sebagai snapshot dan hanya dapat dilihat.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Nama Produk</th>
                                <th>Detail Steaming</th>
                                <th>Catatan</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>Status SPV</th>
                                <th>Catatan SPV</th>
                                <th>Waktu Snapshot</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $item)
                                @php
                                    $steamingRows = json_decode($item->steaming ?? '[]', true);
                                    $catatanRows = json_decode($item->catatan ?? '[]', true);

                                    $steamingRows = is_array($steamingRows) ? $steamingRows : [];
                                    $catatanRows = is_array($catatanRows) ? $catatanRows : [];
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        {{ $data->firstItem() + $loop->index }}
                                    </td>

                                    <td class="text-center">
                                        {{ $item->date ? $item->date->format('d-m-Y') : '-' }}
                                    </td>

                                    <td class="text-center">{{ $item->shift ?? '-' }}</td>
                                    <td>{{ $item->nama_produk ?? '-' }}</td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse" data-bs-target="#steamingAudit{{ $item->uuid }}">
                                            Lihat {{ count($steamingRows) }} Pemeriksaan
                                        </button>
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="collapse" data-bs-target="#catatanAudit{{ $item->uuid }}">
                                            Lihat {{ count($catatanRows) }} Catatan
                                        </button>
                                    </td>

                                    <td>{{ $item->username ?? '-' }}</td>
                                    <td>{{ $item->nama_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        @if ($item->status_spv == 1)
                                            <span class="badge bg-success">Verified</span>
                                        @elseif ($item->status_spv == 2)
                                            <span class="badge bg-danger">Revision</span>
                                        @else
                                            <span class="badge bg-secondary">Created</span>
                                        @endif
                                    </td>

                                    <td>{{ $item->catatan_spv ?? '-' }}</td>

                                    <td class="text-center">
                                        {{ $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : '-' }}
                                    </td>
                                </tr>

                                <tr class="collapse" id="steamingAudit{{ $item->uuid }}">
                                    <td colspan="11">
                                        <div class="p-2">
                                            <h6 class="fw-bold">Detail Pemeriksaan Steaming</h6>

                                            @if (count($steamingRows))
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Field</th>
                                                                @foreach ($steamingRows as $index => $row)
                                                                    <th class="text-center">
                                                                        Pemeriksaan {{ $index + 1 }}
                                                                    </th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $fieldLabels = [
                                                                    'kode_produksi' => 'Kode Produksi',
                                                                    'suhu_rm' => 'Suhu RM',
                                                                    'jumlah_tray' => 'Jumlah Tray',
                                                                    'suhu_ruang' => 'Suhu Ruang',
                                                                    'suhu_produk' => 'Suhu Produk',
                                                                    'suhu_after' => 'Suhu After',
                                                                    'waktu' => 'Waktu',
                                                                    'keterangan' => 'Keterangan',
                                                                    'jam_mulai' => 'Jam Mulai',
                                                                    'jam_selesai' => 'Jam Selesai',
                                                                    'kematangan' => 'Kematangan',
                                                                    'rasa' => 'Rasa',
                                                                    'aroma' => 'Aroma',
                                                                    'tekstur' => 'Tekstur',
                                                                    'warna' => 'Warna',
                                                                ];
                                                            @endphp

                                                            @foreach ($fieldLabels as $field => $label)
                                                                <tr>
                                                                    <th>{{ $label }}</th>

                                                                    @foreach ($steamingRows as $row)
                                                                        <td class="text-center">
                                                                            @if ($field === 'kematangan' || $field === 'rasa' || $field === 'aroma' || $field === 'tekstur' || $field === 'warna')
                                                                                {{ $row['sensori'][$field] ?? '-' }}
                                                                            @else
                                                                                {{ $row[$field] ?? '-' }}
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-muted">Tidak ada detail pemeriksaan.</div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <tr class="collapse" id="catatanAudit{{ $item->uuid }}">
                                    <td colspan="11">
                                        <div class="p-2">
                                            <h6 class="fw-bold">Catatan Penggunaan Steamer</h6>

                                            @if (count($catatanRows))
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Pukul Awal</th>
                                                                <th>Pukul Akhir</th>
                                                                <th>Catatan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($catatanRows as $catatan)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $catatan['pukul_awal'] ?? '-' }}</td>
                                                                    <td>{{ $catatan['pukul_akhir'] ?? '-' }}</td>
                                                                    <td>{{ $catatan['isi'] ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-muted">Tidak ada catatan.</div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        Belum ada data Audit Steamer.
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

    <style>
        .table th,
        .table td {
            white-space: nowrap;
            font-size: 0.85rem;
            vertical-align: middle;
        }
    </style>
@endsection

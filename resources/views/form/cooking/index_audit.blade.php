
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-clipboard-check"></i>
                    Audit Pemeriksaan Pemasakan Produk di Steam/Cooking Kettle
                </h3>

                <a href="{{ route('cooking.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="GET"
                  action="{{ route('cooking.audit') }}"
                  class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <input type="date"
                       name="date"
                       class="form-control"
                       style="max-width:220px"
                       value="{{ request('date') }}">

                <input type="text"
                       name="search"
                       class="form-control"
                       style="max-width:350px"
                       value="{{ request('search') }}"
                       placeholder="Cari produk, kode produksi, username, mesin...">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('cooking.audit') }}" class="btn btn-secondary">
                    Reset
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Nama Produk</th>
                            <th>Sub Produk</th>
                            <th>Jenis Produk</th>
                            <th>Kode Produksi</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Nama Mesin</th>
                            <th>Detail Pemasakan</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Waktu Snapshot</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $item)
                            @php
                                $namaMesin = json_decode($item->nama_mesin, true);
                                $pemasakan = json_decode($item->pemasakan, true);
                                $catatan = json_decode($item->catatan, true);

                                if (!is_array($namaMesin)) {
                                    $namaMesin = [$item->nama_mesin];
                                }

                                if (!is_array($pemasakan)) {
                                    $pemasakan = [];
                                }

                                if (!is_array($catatan)) {
                                    $catatan = !empty($item->catatan)
                                        ? [['isi' => $item->catatan]]
                                        : [];
                                }
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td>{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d-m-Y') : '-' }}</td>
                                <td class="text-center">{{ $item->shift ?? '-' }}</td>
                                <td>{{ $item->nama_produk ?? '-' }}</td>
                                <td>{{ $item->sub_produk ?? '-' }}</td>
                                <td>{{ $item->jenis_produk ?? '-' }}</td>
                                <td>{{ $item->kode_produksi ?? '-' }}</td>
                                <td>{{ $item->waktu_mulai ?? '-' }}</td>
                                <td>{{ $item->waktu_selesai ?? '-' }}</td>

                                <td>
                                    @forelse($namaMesin as $mesin)
                                        <div>{{ is_array($mesin) ? json_encode($mesin) : $mesin }}</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-info text-white"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#pemasakan{{ $item->uuid }}">
                                        Lihat Detail
                                    </button>
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-secondary"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#catatan{{ $item->uuid }}">
                                        Lihat Catatan
                                    </button>
                                </td>

                                <td>{{ $item->username ?? '-' }}</td>
                                <td>
                                    {{ $item->nama_produksi ?? '-' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $item->tgl_update_produksi ? \Carbon\Carbon::parse($item->tgl_update_produksi)->format('d-m-Y H:i') : '-' }}
                                    </small>
                                </td>
                                <td>
                                    {{ $item->nama_spv ?? '-' }}
                                    <br>
                                    <small class="text-muted">
                                        @if($item->status_spv == 1)
                                            <span class="text-success fw-bold">Verified</span>
                                        @elseif($item->status_spv == 2)
                                            <span class="text-danger fw-bold">Revision</span>
                                        @else
                                            <span class="text-secondary">Belum diverifikasi</span>
                                        @endif
                                        <br>
                                        {{ $item->tgl_update_spv ? \Carbon\Carbon::parse($item->tgl_update_spv)->format('d-m-Y H:i') : '-' }}
                                    </small>
                                </td>
                                <td>
                                    {{ $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : '-' }}
                                </td>
                            </tr>

                            <tr class="collapse" id="pemasakan{{ $item->uuid }}">
                                <td colspan="16">
                                    <div class="p-3 bg-light rounded">
                                        <h6 class="fw-bold">Detail Pemasakan</h6>

                                        @forelse($pemasakan as $index => $row)
                                            <div class="border rounded bg-white p-3 mb-3">
                                                <strong>Proses {{ $index + 1 }}</strong>

                                                <div class="table-responsive mt-2">
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <tbody>
                                                            @foreach($row as $key => $value)
                                                                <tr>
                                                                    <th style="width:220px">
                                                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                                                    </th>
                                                                    <td>
                                                                        @if(is_array($value))
                                                                            {{ json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}
                                                                        @else
                                                                            {{ $value !== '' && $value !== null ? $value : '-' }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @empty
                                            <span class="text-muted">Tidak ada detail pemasakan.</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>

                            <tr class="collapse" id="catatan{{ $item->uuid }}">
                                <td colspan="16">
                                    <div class="p-3 bg-light rounded">
                                        <h6 class="fw-bold">Catatan Pemeriksaan</h6>

                                        @forelse($catatan as $note)
                                            <div class="border rounded bg-white p-2 mb-2">
                                                @if(!empty($note['pukul_awal']) || !empty($note['pukul_akhir']))
                                                    <strong>
                                                        {{ $note['pukul_awal'] ?? '-' }}
                                                        -
                                                        {{ $note['pukul_akhir'] ?? '-' }}
                                                    </strong>
                                                    <br>
                                                @endif
                                                {{ is_array($note) ? ($note['isi'] ?? json_encode($note)) : $note }}
                                            </div>
                                        @empty
                                            <span class="text-muted">Tidak ada catatan.</span>
                                        @endforelse

                                        @if(!empty($item->catatan_spv))
                                            <hr>
                                            <strong>Catatan SPV:</strong>
                                            {{ $item->catatan_spv }}
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center">
                                    Belum ada data Audit Cooking.
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

@extends('layouts.app')

@section('content')
    <div class="container-fluid py-2">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <h3 class="mb-0">
                        <i class="bi bi-clipboard-check"></i>
                        Audit Parameter Produk Saus Yoshinoya
                    </h3>

                    <a href="{{ route('yoshinoya.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <form method="GET" action="{{ route('yoshinoya.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                    <input type="date" name="date" class="form-control" style="max-width: 220px"
                        value="{{ request('date') }}">

                    <input type="text" name="search" class="form-control" style="max-width: 300px"
                        placeholder="Cari kode produksi, saus, username..." value="{{ request('search') }}">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>

                    <a href="{{ route('yoshinoya.audit') }}" class="btn btn-secondary">
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
                                <th>Jenis Saus</th>
                                <th>Kode Produksi</th>
                                <th>Suhu Pengukuran</th>
                                <th>Brix (%)</th>
                                <th>Salt (%)</th>
                                <th>Viscositas</th>
                                <th>Brookfield Sebelum</th>
                                <th>Brookfield Frozen</th>
                                <th>Catatan</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>Status SPV</th>
                                <th>Waktu Audit</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $i => $row)
                                @php
                                    $formatNilai = function ($value) {
                                        if (is_string($value)) {
                                            $decoded = json_decode($value, true);

                                            if (json_last_error() === JSON_ERROR_NONE) {
                                                $value = $decoded;
                                            }
                                        }

                                        if (is_array($value)) {
                                            $value = array_filter($value, fn($v) => $v !== null && $v !== '');

                                            return count($value) ? implode(', ', $value) : '-';
                                        }

                                        return $value !== null && $value !== '' ? $value : '-';
                                    };
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        {{ $data->firstItem() + $i }}
                                    </td>

                                    <td class="text-center">
                                        {{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '-' }}
                                    </td>

                                    <td class="text-center">{{ $row->shift ?? '-' }}</td>
                                    <td>{{ $row->saus ?? '-' }}</td>
                                    <td>{{ $row->kode_produksi ?? '-' }}</td>
                                    <td class="text-center">{{ $row->suhu_pengukuran ?? '-' }}</td>
                                    <td>{{ $formatNilai($row->brix) }}</td>
                                    <td>{{ $formatNilai($row->salt) }}</td>
                                    <td>{{ $formatNilai($row->visco) }}</td>
                                    <td>{{ $row->brookfield_sebelum ?? '-' }}</td>
                                    <td>{{ $row->brookfield_frozen ?? '-' }}</td>
                                    <td>{{ $row->catatan ?? '-' }}</td>
                                    <td>{{ $row->username ?? '-' }}</td>
                                    <td>{{ $row->nama_produksi ?? '-' }}</td>

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
                                        {{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="text-center py-4">
                                        Belum ada data Audit Yoshinoya.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $data->links() }}
                </div>

            </div>
        </div>
    </div>
@endsection

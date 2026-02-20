@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-trash"></i> Recycle Bin Peneraan Thermometer</h3>
                <a href="{{ route('thermometer.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th rowspan="2">No.</th>
                            <th rowspan="2">Date | Shift</th>
                            <th>Kode Thermometer</th>
                            <th>Area</th>
                            <th>Standar (0.0°C)</th>
                            <th>Pukul</th>
                            <th>Hasil Tera</th>
                            <th>Tindakan Perbaikan</th>
                            <th rowspan="2">QC</th>
                            <th rowspan="2">Dihapus Pada</th>
                            <th rowspan="2">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $no = 1; @endphp
                        @forelse ($thermometer as $dep)
                        @php
                            $kode_thermometer = is_array($dep->kode_thermometer) ? $dep->kode_thermometer : json_decode($dep->kode_thermometer, true) ?? [];
                            $area             = is_array($dep->area) ? $dep->area : json_decode($dep->area, true) ?? [];
                            $standar          = is_array($dep->standar) ? $dep->standar : json_decode($dep->standar, true) ?? [];
                            $waktu_tera       = is_array($dep->waktu_tera) ? $dep->waktu_tera : json_decode($dep->waktu_tera, true) ?? [];
                            $hasil_tera       = is_array($dep->hasil_tera) ? $dep->hasil_tera : json_decode($dep->hasil_tera, true) ?? [];
                            $tindakan_koreksi = is_array($dep->tindakan_koreksi) ? $dep->tindakan_koreksi : json_decode($dep->tindakan_koreksi, true) ?? [];
                            $rowspan          = max(count($kode_thermometer), 1);
                        @endphp

                        @foreach($kode_thermometer as $i => $kode)
                        <tr>
                            @if($i==0)
                            <td rowspan="{{ $rowspan }}" class="text-center align-middle">{{ $no++ }}</td>
                            <td rowspan="{{ $rowspan }}" class="text-center align-middle">
                                {{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }} | Shift: {{ $dep->shift ?? '-' }}
                            </td>
                            @endif

                            <td>{{ $kode }}</td>
                            <td>{{ $area[$i] ?? '-' }}</td>
                            <td>{{ $standar[$i] ?? '0.0' }}</td>
                            <td>{{ $waktu_tera[$i] ?? '-' }}</td>
                            <td class="{{ abs((float) str_replace(',', '.', $hasil_tera[$i] ?? 0)) > 0.4 ? 'text-danger fw-bold' : '' }}">
                                {{ $hasil_tera[$i] ?? '-' }}
                            </td>
                            <td>{{ $tindakan_koreksi[$i] ?? '-' }}</td>

                            @if($i==0)
                            <td class="text-center align-middle" rowspan="{{ $rowspan }}">{{ $dep->username ?? '-' }}</td>
                            <td class="text-center align-middle" rowspan="{{ $rowspan }}">
                                {{ $dep->deleted_at ? \Carbon\Carbon::parse($dep->deleted_at)->format('d-m-Y H:i') : '-' }}
                            </td>
                            <td class="text-center align-middle" rowspan="{{ $rowspan }}">
                                <form action="{{ route('thermometer.restore', $dep->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('thermometer.deletePermanent', $dep->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm mb-1"
                                        onclick="return confirm('Hapus permanen?')">
                                        <i class="bi bi-x-circle"></i> Delete
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="11" class="text-center align-middle">Recycle bin kosong.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-2">
                {{ $thermometer->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

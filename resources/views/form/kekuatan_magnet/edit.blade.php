@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4>
                <i class="bi bi-pencil-square"></i> Edit Pemeriksaan Kekuatan Magnet Trap
            </h4>
            <form method="POST"
            action="{{ route('kekuatan_magnet.update', $kekuatan_magnet->uuid) }}"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')
            {{-- Bagian Identitas --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong>Identitas Pemeriksaan</strong>
                </div>

                <div class="card-body">
                    <div class="row mb-3">

                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date"
                            id="dateInput"
                            name="date"
                            class="form-control"
                            value="{{ old('date', $kekuatan_magnet->date) }}"
                            required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Shift</label>
                            <select id="shiftInput"
                            name="shift"
                            class="form-control"
                            required>
                            <option value="1" {{ old('shift', $kekuatan_magnet->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                            <option value="2" {{ old('shift', $kekuatan_magnet->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                            <option value="3" {{ old('shift', $kekuatan_magnet->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bagian Pemeriksaan --}}
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <strong>Kekuatan Magnet Trap</strong>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Alat</label>
                        <input type="text" id="nama_alat" name="nama_alat" class="form-control" value="{{ old('nama_alat', $kekuatan_magnet->nama_alat) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nilai Pengukuran (Gauss)</label>
                        <input type="text" id="gauss" name="gauss" class="form-control" value="{{ old('gauss', $kekuatan_magnet->gauss) }}" step="0.01">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Kondisi Magnet (Visual)</label>
                        <input type="text" id="kondisi" name="kondisi" class="form-control" value="{{ old('kondisi', $kekuatan_magnet->kondisi) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parameter Setting</label>
                        <select id="parameter" name="parameter" class="form-control">
                            <option value="sesuai" {{ old('parameter', $kekuatan_magnet->parameter) == 'sesuai' ? 'selected' : '' }}>Sesuai
                            </option>
                            <option value="tidak sesuai" {{ old('parameter', $kekuatan_magnet->parameter) == 'tidak sesuai' ? 'selected' : '' }}>Tidak Sesuai
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong>Keterangan</strong>
            </div>

            <div class="card-body">
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan bila ada">{{ old('keterangan', $kekuatan_magnet->keterangan) }}</textarea>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="d-flex justify-content-between mt-3">
            <button type="submit" class="btn btn-primary w-auto">
                <i class="bi bi-save"></i> Update
            </button>
            <a href="{{ route('kekuatan_magnet.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
</div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

@endsection

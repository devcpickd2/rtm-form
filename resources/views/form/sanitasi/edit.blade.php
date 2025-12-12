@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4><i class="bi bi-pencil-square"></i> Form Edit Pemeriksaan Sanitasi</h4>

            <form method="POST" action="{{ route('sanitasi.update', $sanitasi->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- IDENTITAS --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">

                        <div class="row mb-3">

                            {{-- Tanggal --}}
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" 
                                    name="date" 
                                    class="form-control @error('date') is-invalid @enderror"
                                    value="{{ old('date', $sanitasi->date) }}" required>

                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Shift --}}
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select name="shift" 
                                        class="form-control @error('shift') is-invalid @enderror" 
                                        required>
                                    <option value="1" {{ old('shift', $sanitasi->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $sanitasi->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $sanitasi->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>

                                @error('shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pukul --}}
                            <div class="col-md-4">
                                <label class="form-label">Pukul</label>
                                <input type="time" 
                                    name="pukul" 
                                    class="form-control @error('pukul') is-invalid @enderror"
                                    value="{{ old('pukul', $sanitasi->pukul) }}" required>

                                @error('pukul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                    </div>
                </div>

                {{-- PEMERIKSAAN --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Pemeriksaan Area</strong>
                    </div>
                    <div class="card-body">

                        <div class="row mb-3">

                            {{-- Standar Foot Basin --}}
                            <div class="col-md-6">
                                <label class="form-label">Standar Foot Basin (200 ppm)</label>
                                <input type="number" 
                                    id="std_footbasin" 
                                    name="std_footbasin"
                                    class="form-control @error('std_footbasin') is-invalid @enderror"
                                    value="{{ old('std_footbasin', $sanitasi->std_footbasin) }}">

                                @error('std_footbasin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="text-danger mt-1" id="foot-warning" style="display:none;">
                                    Nilai harus 200 ppm!
                                </div>
                            </div>

                            {{-- Upload Foot Basin --}}
                            <div class="col-md-6">
                                <label class="form-label">Aktual Foot Basin (Upload)</label>

                                @if($sanitasi->aktual_footbasin)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/'.$sanitasi->aktual_footbasin) }}" target="_blank">
                                            Lihat Gambar Sebelumnya
                                        </a>
                                    </div>
                                @endif

                                <input type="file" 
                                    name="aktual_footbasin" 
                                    class="form-control @error('aktual_footbasin') is-invalid @enderror"
                                    accept="image/*">

                                @error('aktual_footbasin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        {{-- Hand Basin --}}
                        <div class="row mb-3">

                            {{-- Standar --}}
                            <div class="col-md-6">
                                <label class="form-label">Standar Hand Basin (50 ppm)</label>
                                <input type="number" 
                                    id="std_handbasin" 
                                    name="std_handbasin"
                                    class="form-control @error('std_handbasin') is-invalid @enderror"
                                    value="{{ old('std_handbasin', $sanitasi->std_handbasin) }}">

                                @error('std_handbasin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="text-danger mt-1" id="hand-warning" style="display:none;">
                                    Nilai harus 50 ppm!
                                </div>
                            </div>

                            {{-- Upload --}}
                            <div class="col-md-6">
                                <label class="form-label">Aktual Hand Basin (Upload)</label>

                                @if($sanitasi->aktual_handbasin)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/'.$sanitasi->aktual_handbasin) }}" target="_blank">
                                            Lihat Gambar Sebelumnya
                                        </a>
                                    </div>
                                @endif

                                <input type="file" 
                                    name="aktual_handbasin" 
                                    class="form-control @error('aktual_handbasin') is-invalid @enderror"
                                    accept="image/*">

                                @error('aktual_handbasin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                    </div>
                </div>

                {{-- CATATAN --}}
                <div class="card mb-3">

                    <div class="card-header bg-light"><strong>Keterangan</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" 
                            class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $sanitasi->keterangan) }}</textarea>

                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card-header bg-light"><strong>Tindakan Koreksi</strong></div>
                    <div class="card-body">
                        <textarea name="tindakan_koreksi" 
                            class="form-control @error('tindakan_koreksi') is-invalid @enderror">{{ old('tindakan_koreksi', $sanitasi->tindakan_koreksi) }}</textarea>

                        @error('tindakan_koreksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" 
                            class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan', $sanitasi->catatan) }}</textarea>

                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- BUTTON --}}
                <div class="d-flex justify-content-between">
                    <button class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>

                    <a href="{{ route('sanitasi.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- Warning Real-Time --}}
<script>
document.getElementById("std_footbasin").addEventListener("input", function() {
    document.getElementById("foot-warning").style.display = this.value != 200 ? "block" : "none";
});

document.getElementById("std_handbasin").addEventListener("input", function() {
    document.getElementById("hand-warning").style.display = this.value != 50 ? "block" : "none";
});
</script>

@endsection

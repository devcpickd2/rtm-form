@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4><i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Sanitasi</h4>

            <form method="POST" action="{{ route('sanitasi.store') }}" enctype="multipart/form-data" id="sanitasiForm">
                @csrf

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
                                <input type="date" id="dateInput" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Shift --}}
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control @error('shift') is-invalid @enderror" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift') == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift') == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift') == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                                @error('shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pukul --}}
                            <div class="col-md-4">
                                <label class="form-label">Pukul</label>
                                <input type="time" id="timeInput" name="pukul" class="form-control @error('pukul') is-invalid @enderror" value="{{ old('pukul') }}" required>
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
                            {{-- Foot Basin --}}
                            <div class="col-md-6">
                                <label class="form-label">Standar Foot Basin (200 ppm)</label>
                                <input type="number" name="std_footbasin" id="std_footbasin" class="form-control @error('std_footbasin') is-invalid @enderror" value="{{ old('std_footbasin', 200) }}">
                                @error('std_footbasin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-danger mt-1" id="foot-warning" style="display:none;">Nilai harus 200 ppm!</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Aktual Foot Basin (Upload)</label>
                                <input type="file" name="aktual_footbasin" class="form-control @error('aktual_footbasin') is-invalid @enderror" accept="image/*">
                                @error('aktual_footbasin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Hand Basin --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Standar Hand Basin (50 ppm)</label>
                                <input type="number" name="std_handbasin" id="std_handbasin" class="form-control @error('std_handbasin') is-invalid @enderror" value="{{ old('std_handbasin', 50) }}">
                                @error('std_handbasin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-danger mt-1" id="hand-warning" style="display:none;">Nilai harus 50 ppm!</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Aktual Hand Basin (Upload)</label>
                                <input type="file" name="aktual_handbasin" class="form-control @error('aktual_handbasin') is-invalid @enderror" accept="image/*">
                                @error('aktual_handbasin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-3">
                    <div class="card-header bg-light"><strong>Keterangan</strong></div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="card-header bg-light"><strong>Tindakan Koreksi</strong></div>
                    <div class="card-body">
                        <textarea name="tindakan_koreksi" class="form-control">{{ old('tindakan_koreksi') }}</textarea>
                    </div>

                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="d-flex justify-content-between">
                    <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('sanitasi.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- Auto set tanggal + shift --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    let now = new Date();
    document.getElementById("dateInput").value = now.toISOString().split("T")[0];

    let hh = now.getHours();
    let mm = now.getMinutes().toString().padStart(2, '0');
    document.getElementById("timeInput").value = `${hh}:${mm}`;

    if (hh >= 7 && hh < 15) document.getElementById("shiftInput").value = 1;
    else if (hh >= 15 && hh < 23) document.getElementById("shiftInput").value = 2;
    else document.getElementById("shiftInput").value = 3;
});
</script>

{{-- warning real-time --}}
<script>
document.getElementById("std_footbasin").addEventListener("input", function() {
    document.getElementById("foot-warning").style.display = this.value != 200 ? "block" : "none";
});

document.getElementById("std_handbasin").addEventListener("input", function() {
    document.getElementById("hand-warning").style.display = this.value != 50 ? "block" : "none";
});
</script>

@endsection

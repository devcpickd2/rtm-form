@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Form Edit Parameter Produk Saus</h4>
            <form method="POST" action="{{ route('yoshinoya.update', $yoshinoya->uuid) }}" enctype="multipart/form-yoshinoya">
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
                                <label class="form-label">Tanggal Produksi</label>
                                <input type="date" id="dateInput" name="date" class="form-control" 
                                value="{{ old('date', $yoshinoya->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $yoshinoya->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $yoshinoya->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $yoshinoya->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <strong>Parameter Pengecekan Saus</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Saus</label>
                                <select id="saus" name="saus" class="form-control" required>
                                    <option value="" disabled>Pilih Saus</option>
                                    <option value="Teriyaki" {{ old('saus', $yoshinoya->saus) == 'Teriyaki' ? 'selected' : '' }}>Teriyaki</option>
                                    <option value="Vegetable" {{ old('saus', $yoshinoya->saus) == 'Vegetable' ? 'selected' : '' }}>Vegetable</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" 
                                value="{{ old('kode_produksi', $yoshinoya->kode_produksi) }}" required>
                            </div>
                        </div>
                        
                        {{-- Notes Vegetable --}}
                        <div class="alert alert-warning mt-2 py-2 px-3 note" id="note-vegetable-basic" style="font-size: 0.9rem; display:none;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Vegetable:</strong>  
                            <ul class="mb-0 ps-3">
                                <li>Suhu pengukuran: 24 - 26°C</li>
                                <li>Brix: 6 - 12%</li>
                                <li>Salt: 6 - 12%</li>
                                <li>Viscositas: 20 - 50 detik.milidetik</li>
                            </ul>
                        </div>

                        {{-- Notes Teriyaki --}}
                        <div class="alert alert-warning mt-2 py-2 px-3 note" id="note-teriyaki-basic" style="font-size: 0.9rem; display:none;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Teriyaki:</strong>  
                            <ul class="mb-0 ps-3">
                                <li>Suhu pengukuran: 24 - 26°C</li>
                                <li>Brix: 33 - 38%</li>
                                <li>Salt: 14 - 17%</li>
                                <li>Viscositas: 70 - 130 detik.milidetik</li>
                            </ul>
                        </div>


                        @php
                        $brix  = json_decode($yoshinoya->brix, true) ?? [];
                        $salt  = json_decode($yoshinoya->salt, true) ?? [];
                        $visco = json_decode($yoshinoya->visco, true) ?? [];
                        @endphp

                        <div class="row mb-3">
                            <!-- Suhu Pengukuran -->
                            <div class="col-md-6">
                                <label class="form-label">Suhu Pengukuran (°C)</label>
                                <input type="text" 
                                id="suhu_pengukuran" 
                                name="suhu_pengukuran" 
                                class="form-control" 
                                value="{{ old('suhu_pengukuran', $yoshinoya->suhu_pengukuran) }}">
                            </div>

                            <!-- Brix -->
                            <div class="col-md-6">
                                <label class="form-label">Brix (%)</label>
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="brix1" name="brix[]" class="form-control" 
                                        value="{{ old('brix.0', $brix[0] ?? '') }}">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="brix2" name="brix[]" class="form-control" 
                                        value="{{ old('brix.1', $brix[1] ?? '') }}">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="brix3" name="brix[]" class="form-control" 
                                        value="{{ old('brix.2', $brix[2] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Salt -->
                            <div class="col-md-6">
                                <label class="form-label">Salt (%)</label>
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="salt1" name="salt[]" class="form-control" 
                                        value="{{ old('salt.0', $salt[0] ?? '') }}">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="salt2" name="salt[]" class="form-control" 
                                        value="{{ old('salt.1', $salt[1] ?? '') }}">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="salt3" name="salt[]" class="form-control" 
                                        value="{{ old('salt.2', $salt[2] ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Viscositas -->
                            <div class="col-md-6">
                                <label class="form-label">Viscositas (detik.milidetik)</label>
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="visco1" name="visco[]" class="form-control" 
                                        value="{{ old('visco.0', $visco[0] ?? '') }}">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="visco2" name="visco[]" class="form-control" 
                                        value="{{ old('visco.1', $visco[1] ?? '') }}">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="visco3" name="visco[]" class="form-control" 
                                        value="{{ old('visco.2', $visco[2] ?? '') }}">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="visco4" name="visco[]" class="form-control" 
                                        value="{{ old('visco.3', $visco[3] ?? '') }}">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <input type="text" id="visco5" name="visco[]" class="form-control" 
                                        value="{{ old('visco.4', $visco[4] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-2 py-2 px-3 note" id="note-vegetable-brookfield" style="font-size: 0.9rem; display:none;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Vegetable:</strong>  
                            <ul class="mb-0 ps-3">
                                <li>Brookfield LV, S 64,. 30% RPM suhu saus 24 - 26°C: 1000 - 3000 Cp</li>
                                <li>Brookfield LV, S 64,. 30% RPM (Setelah Frozen) suhu saus 24 - 26°C: 1000 - 3000 Cp</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning mt-2 py-2 px-3 note" id="note-teriyaki-brookfield" style="font-size: 0.9rem; display:none;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Teriyaki:</strong>  
                            <ul class="mb-0 ps-3">
                                <li>Brookfield LV, S 64,. 30% RPM suhu saus 24 - 26°C: 3000 - 5000 Cp</li>
                                <li>Brookfield LV, S 64,. 30% RPM (Setelah Frozen) suhu saus 24 - 26°C: 2500 - 3000 Cp</li>
                            </ul>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Brookfield LV, S 64,. 30% RPM suhu saus 24 - 26°C</label>
                                <input type="text" id="brookfield_sebelum" name="brookfield_sebelum" class="form-control" 
                                value="{{ old('brookfield_sebelum', $yoshinoya->brookfield_sebelum) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brookfield LV, S 64,. 30% RPM (Setelah Frozen) suhu saus 24 - 26°C</label>
                                <input type="text" id="brookfield_frozen" name="brookfield_frozen" class="form-control" 
                                value="{{ old('brookfield_frozen', $yoshinoya->brookfield_frozen) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-3">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $yoshinoya->catatan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('yoshinoya.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sausSelect = document.getElementById('saus');
        const notes = document.querySelectorAll('.note');

        function showNotes() {
            notes.forEach(note => note.style.display = 'none');
            if(sausSelect.value === 'Vegetable') {
                document.getElementById('note-vegetable-basic').style.display = 'block';
                document.getElementById('note-vegetable-brookfield').style.display = 'block';
            } else if(sausSelect.value === 'Teriyaki') {
                document.getElementById('note-teriyaki-basic').style.display = 'block';
                document.getElementById('note-teriyaki-brookfield').style.display = 'block';
            }
        }

        // tampilkan notes saat page load sesuai yoshinoya lama
        showNotes();

        // tampilkan notes saat dropdown berubah
        sausSelect.addEventListener('change', showNotes);
    });
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Peneraan Thermometer</h4>
            <form method="POST" action="{{ route('thermometer.update', $thermometer->uuid) }}" enctype="multipart/form-data">
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
                                <input type="date" id="dateInput" name="date" class="form-control" value="{{ $thermometer->date }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ $thermometer->shift == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ $thermometer->shift == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ $thermometer->shift == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                   <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <strong>Peneraan Timbangan</strong>
                    <button type="button" class="btn btn-light btn-sm" id="addRow">
                      <i class="bi bi-plus-circle"></i> Tambah Pemeriksaan
                  </button>
              </div>

              <div class="card-body">

                {{-- Notes --}}
                <div class="alert alert-warning mt-2 py-2 px-3" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle"></i>
                    <strong>Catatan:</strong>  
                    <ul class="mb-0 ps-3">
                        <li>Tera termometer dilakukan di setiap awal produksi</li>
                        <li>Termometer ditera dengan memasukkan sensor di es (0 °C)</li>
                        <li>Jika ada selisih angka display suhu dengan suhu standar es, beri keterangan <strong>(+)</strong> atau <strong>(-)</strong> angka selisih (faktor koreksi)</li>
                        <li>Jika faktor koreksi &gt; 0,4 °C, thermometer perlu perbaikan</li>
                    </ul>
                </div>
                <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle"></i>
                    <strong>Standar Tera:</strong>  
                    <ul class="mb-0 ps-3">
                        <li>0.0°C</li>
                    </ul>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered text-center align-middle" id="pemeriksaanTable">

                    <table class="table" id="pemeriksaanTable">
                        <thead>
                            <tr>
                                <th>Kode Thermometer</th>
                                <th>Area</th>
                                <th>Pukul</th>
                                <th>Hasil Tera</th>
                                <th>Tindakan Koreksi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($thermometer->kode_thermometer as $i => $kode)
                            <tr class="pemeriksaan-row">
                                <td><input type="text" name="kode_thermometer[]" 
                                    value="{{ old('kode_thermometer.'.$i, $kode) }}" class="form-control" required></td>
                                    <td><input type="text" name="area[]" 
                                        value="{{ old('area.'.$i, $thermometer->area[$i] ?? '') }}" class="form-control" required></td>
                                        <td><input type="time" name="waktu_tera[]" 
                                            value="{{ old('waktu_tera.'.$i, $thermometer->waktu_tera[$i] ?? '') }}" class="form-control"></td>
                                            <td><input type="text" name="hasil_tera[]" 
                                                value="{{ old('hasil_tera.'.$i, $thermometer->hasil_tera[$i] ?? '') }}" class="form-control hasil_tera" required>
                                                <div class="text-danger small mt-1 d-none hasil-warning">⚠️ Suhu melebihi standar!, perlu diperbaiki</div>
                                            </td>
                                            <td><textarea name="tindakan_koreksi[]" class="form-control" rows="1">{{ old('tindakan_koreksi.'.$i, $thermometer->tindakan_koreksi[$i] ?? '') }}</textarea></td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm removeRow">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="card-header bg-light">
                                <strong>Catatan</strong>
                            </div>
                            <div class="card-body">
                                <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ $thermometer->catatan }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-primary w-auto">
                            <i class="bi bi-save"></i> Update
                        </button>
                        <a href="{{ route('thermometer.index') }}" class="btn btn-secondary w-auto">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
     document.addEventListener("DOMContentLoaded", function () {
        const tableBody = document.querySelector('#pemeriksaanTable tbody');

        tableBody.addEventListener('input', function(e) {
            if (e.target.classList.contains('hasil_tera')) {
                let value = parseFloat(e.target.value);
                const warning = e.target.closest('td').querySelector('.hasil-warning');

                if (!isNaN(value) && Math.abs(value) > 0.4) {
                warning.classList.remove('d-none'); // tampilkan
                e.target.classList.add('border-danger'); // border merah
            } else {
                warning.classList.add('d-none'); // sembunyikan
                e.target.classList.remove('border-danger');
            }
        }
    });
    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tableBody = document.querySelector('#pemeriksaanTable tbody');
        const addRowBtn = document.getElementById('addRow');

        addRowBtn.addEventListener('click', () => {
            const firstRow = tableBody.querySelector('.pemeriksaan-row');
            const clone = firstRow.cloneNode(true);
            clone.querySelectorAll('input, textarea').forEach(el => el.value = '');
            tableBody.appendChild(clone);
        });

        tableBody.addEventListener('click', e => {
            if (e.target.closest('.removeRow')) {
                const row = e.target.closest('tr');
                const rows = tableBody.querySelectorAll('.pemeriksaan-row');
                if (rows.length > 1) {
                    row.remove();
                } else {
                    alert('Minimal 1 pemeriksaan harus ada.');
                }
            }
        });
    });
</script>
@endsection

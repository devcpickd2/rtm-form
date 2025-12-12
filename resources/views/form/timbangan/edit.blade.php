@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Peneraan Timbangan</h4>
            <form method="POST" action="{{ route('timbangan.update', $timbangan->uuid) }}" enctype="multipart/form-data">
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
                                <input type="date" id="dateInput" name="date" 
                                class="form-control" 
                                value="{{ old('date', $timbangan->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $timbangan->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $timbangan->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $timbangan->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <strong>Peneraan Timbangan</strong>
                    <button type="button" class="btn btn-light btn-sm" id="addRow">
                      <i class="bi bi-plus-circle"></i> Tambah Pemeriksaan
                  </button>
              </div>

              <div class="card-body">
                <div class="alert alert-warning mt-2 py-2 px-3" style="font-size: 0.9rem;">
                  <i class="bi bi-info-circle"></i>
                  <strong>Catatan:</strong>
                  <ul class="mb-0 ps-3">
                    <li>Tera timbangan dilakukan di setiap awal produksi</li>
                    <li>Timbangan ditera menggunakan anak timbangan standar</li>
                    <li>Jika ada selisih angka timbang dengan berat timbangan standar, beri keterangan (+) atau (-)</li>
                </ul>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered text-center align-middle" id="pemeriksaanTable">
               
                <table class="table" id="pemeriksaanTable">
                    <thead>
                        <tr>
                            <th>Kode Timbangan</th>
                            <th>Standar (gr)</th>
                            <th>Pukul</th>
                            <th>Hasil Tera</th>
                            <th>Tindakan Perbaikan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timbangan->kode_timbangan as $i => $kode)
                        <tr class="pemeriksaan-row">
                            <td><input type="text" name="kode_timbangan[]" 
                                value="{{ old('kode_timbangan.'.$i, $kode) }}" class="form-control" required></td>
                                <td><input type="text" name="standar[]" 
                                    value="{{ old('standar.'.$i, $timbangan->standar[$i] ?? '') }}" class="form-control" required></td>
                                    <td><input type="time" name="waktu_tera[]" 
                                        value="{{ old('waktu_tera.'.$i, $timbangan->waktu_tera[$i] ?? '') }}" class="form-control"></td>
                                        <td><input type="text" name="hasil_tera[]" 
                                            value="{{ old('hasil_tera.'.$i, $timbangan->hasil_tera[$i] ?? '') }}" class="form-control" required></td>
                                            <td><textarea name="tindakan_perbaikan[]" class="form-control" rows="1">{{ old('tindakan_perbaikan.'.$i, $timbangan->tindakan_perbaikan[$i] ?? '') }}</textarea></td>
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
                            <strong>Catatan</strong>
                        </div>
                        <div class="card-body">
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $timbangan->catatan) }}</textarea>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-success w-auto">
                            <i class="bi bi-save"></i> Update
                        </button>
                        <a href="{{ route('timbangan.index') }}" class="btn btn-secondary w-auto">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

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

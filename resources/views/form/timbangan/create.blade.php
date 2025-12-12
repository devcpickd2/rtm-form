@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Peneraan Timbangan</h4>
            <form method="POST" action="{{ route('timbangan.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <strong>Peneraan Timbangan</strong>
                    <button type="button" class="btn btn-light btn-sm" id="addRow">
                      <i class="bi bi-plus-circle"></i> Tambah Pemeriksaan</button>
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
                        <thead class="table-light">
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
                      <tr class="pemeriksaan-row">
                        <td><input type="text" name="kode_timbangan[]" class="form-control" required></td>
                        <td><input type="text" name="standar[]" class="form-control" required></td>
                        <td><input type="time" name="waktu_tera[]" class="form-control"></td>
                        <td><input type="text" name="hasil_tera[]" class="form-control" required></td>
                        <td><textarea name="tindakan_perbaikan[]" class="form-control" rows="1"></textarea></td>
                        <td>
                          <button type="button" class="btn btn-danger btn-sm removeRow">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>

@push('scripts')
<script>
    document.getElementById('addRow').addEventListener('click', function() {
        let tableBody = document.querySelector('#pemeriksaanTable tbody');
        let firstRow = tableBody.querySelector('.pemeriksaan-row');
        let clone = firstRow.cloneNode(true);

    // Kosongkan input pada clone
        clone.querySelectorAll('input, textarea').forEach(el => el.value = '');
        tableBody.appendChild(clone);
    });

// Event hapus row (delegasi)
    document.querySelector('#pemeriksaanTable tbody').addEventListener('click', function(e) {
        if (e.target.closest('.removeRow')) {
            let row = e.target.closest('tr');
            let rows = document.querySelectorAll('#pemeriksaanTable tbody .pemeriksaan-row');
            if (rows.length > 1) {
                row.remove();
            } else {
                alert('Minimal 1 pemeriksaan harus ada.');
            }
        }
    });
</script>
@endpush


{{-- Notes --}}
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="card-header bg-light">
            <strong>Catatan</strong>
        </div>
        <div class="card-body">
            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada"></textarea>
        </div>
    </div>
</div>

{{-- Tombol --}}
<div class="d-flex justify-content-between mt-3">
    <button class="btn btn-success w-auto">
        <i class="bi bi-save"></i> Simpan
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
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const timeInput = document.getElementById("timeInput");
        const shiftInput = document.getElementById("shiftInput");

    // Ambil waktu sekarang
        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');
        let min = String(now.getMinutes()).padStart(2, '0');

    // Set value tanggal dan jam
        dateInput.value = `${yyyy}-${mm}-${dd}`;
        timeInput.value = `${hh}:${min}`;

    // Tentukan shift berdasarkan jam
        let hour = parseInt(hh);
        if (hour >= 7 && hour < 15) {
            shiftInput.value = "1";
        } else if (hour >= 15 && hour < 23) {
            shiftInput.value = "2";
        } else {
            shiftInput.value = "3"; 
        }

    });
</script>
@endsection

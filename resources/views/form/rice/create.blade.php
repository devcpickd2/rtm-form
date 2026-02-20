@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Pemasakan dengan Rice Cooker</h4>
            <form method="POST" action="{{ route('rice.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Bagian Identitas --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control"
                                value="{{ old('date', $data->date ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $data->shift ?? '') == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $data->shift ?? '') == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $data->shift ?? '') == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $data->nama_produk ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan rice --}} 
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Pemasakan dengan Rice Cooker</strong>
                        <button type="button" id="addriceColumn" class="btn btn-primary btn-sm">
                            + Tambah Pemeriksaan
                        </button>
                    </div>
                    <div class="card-body table-responsive" style="overflow-x:auto;">
                        {{-- Note Petunjuk Checkbox --}}
                        <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>  
                            <i class="bi bi-check-circle text-success"></i>  Checkbox apabila hasil <u>Oke</u>.  
                            Kosongkan Checkbox apabila hasil <u>Tidak Oke</u>.  
                        </div>

                        <div class="alert alert-warning mt-2 py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>  
                            <b>USAHAKAN PAKEK TITIK (.) JANGAN PAKAI KOMA(,)</b>.  
                        </div>

                        <table class="table table-bordered table-sm text-center align-middle" id="riceTable">
                            <thead class="table-light">
                                <tr id="headerRow">
                                    <th style="min-width: 220px; text-align: left;">Parameter</th>
                                    <th>Pemeriksaan 1</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Section cooker --}}
                                <tr><td class="text-left">Kode Beras</td><td><input type="text" name="cooker[0][kode_beras]" class="form-control form-control-sm"></td></tr>
                                <tr><td class="text-left">Berat (kg)</td><td><input type="number" name="cooker[0][berat]" class="form-control form-control-sm" step="0.01"></td></tr>
                                <tr><td class="text-left">Kode Produksi</td><td><input type="text" name="cooker[0][kode_produksi]" class="form-control form-control-sm"></td></tr>
                                <tr><td class="text-left">Basket No.</td><td><input type="number" name="cooker[0][basket]" class="form-control form-control-sm"></td></tr>
                                <tr class="section-header"><td colspan="2" class="text-left fw-bold bg-light">RICE COOKER</td></tr>
                                <tr><td class="text-left">Gas ON/OFF</td><td>
                                    <select name="cooker[0][gas]" class="form-control form-control-sm">
                                      <option value="ON">ON</option>
                                      <option value="OFF">OFF</option></select>
                                  </td>
                              </tr>
                              <tr><td class="text-left">Waktu (Menit)</td><td><input type="number" name="cooker[0][waktu_masak]" class="form-control form-control-sm" step="0.01"></td></tr>
                              <tr><td class="text-left">Suhu Produk (°C)</td><td><input type="number" name="cooker[0][suhu_produk]" class="form-control form-control-sm" step="0.01"></td></tr>
                              <tr><td class="text-left">Suhu Produk setelah 1 Menit (°C)</td><td><input type="number" name="cooker[0][suhu_after]" class="form-control form-control-sm" step="0.01"></td></tr>
                              <tr><td class="text-left">Suhu After Vacuum (°C)</td><td><input type="number" name="cooker[0][suhu_vacuum]" class="form-control form-control-sm" step="0.01"></td></tr>

                              {{-- Section LAMA PROSES --}}
                              <tr class="section-header"><td colspan="2" class="text-left fw-bold bg-light">LAMA PROSES</td></tr>
                              <tr><td class="text-left">Jam Mulai</td><td><input type="time" name="cooker[0][jam_mulai]" class="form-control form-control-sm"></td></tr>
                              <tr><td class="text-left">Jam Selesai</td><td><input type="time" name="cooker[0][jam_selesai]" class="form-control form-control-sm"></td></tr>

                              {{-- Section SENSORI --}}
                              <tr class="section-header"><td colspan="2" class="text-left fw-bold bg-light">SENSORI</td></tr>
                              <tr><td class="text-left">Kematangan</td><td><input type="checkbox" name="cooker[0][sensori][kematangan]" value="Oke" class="big-checkbox"></td></tr>
                              <tr><td class="text-left">Rasa</td><td><input type="checkbox" name="cooker[0][sensori][rasa]" value="Oke" class="big-checkbox"></td></tr>
                              <tr><td class="text-left">Aroma</td><td><input type="checkbox" name="cooker[0][sensori][aroma]" value="Oke" class="big-checkbox"></td></tr>
                              <tr><td class="text-left">Tekstur</td><td><input type="checkbox" name="cooker[0][sensori][tekstur]" value="Oke" class="big-checkbox"></td></tr>
                              <tr><td class="text-left">Warna</td><td><input type="checkbox" name="cooker[0][sensori][warna]" value="Oke" class="big-checkbox"></td></tr>

                              {{-- Section Aksi --}}
                              <tr><td class="text-left">Aksi</td><td><button type="button" class="btn btn-danger btn-sm removeColumn">Hapus</button></td></tr>
                          </tbody>
                      </table>
                  </div>
              </div>

              {{-- Catatan --}}
              <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>Catatan</strong>
                </div>
                <div class="card-body">
                    <textarea name="catatan" class="form-control" rows="3"
                    placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-success w-auto">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('rice.index') }}" class="btn btn-secondary w-auto">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
</div>
<!-- jQuery dulu (wajib) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap-Select CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>
<style>
    #riceTable th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
    }
    #riceTable td {
        padding: 10px;
        vertical-align: middle;
        text-align: center;
    }
    #riceTable tbody tr:nth-child(odd) {
        background-color: #f9f9f9;
    }
    #riceTable tbody tr:hover {
        background-color: #e9f7fe;
    }
    .form-control-sm {
        min-width: 120px;
    }
    .big-checkbox {
        width: 24px;
        height: 24px;
        transform: scale(1.4);
        cursor: pointer;
    }
    .section-header td {
        background: #f1f3f4 !important;
        font-weight: bold;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");
        const addColumnBtn = document.getElementById("addriceColumn");
        const headerRow = document.getElementById("headerRow");
        const riceTable = document.getElementById("riceTable");
        let columnIndex = 1;

        // Set tanggal & shift default
        if (!dateInput.value) {
            let now = new Date();
            dateInput.value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
        }
        if (!shiftInput.value) {
            let hour = new Date().getHours();
            shiftInput.value = (hour >= 7 && hour < 15) ? "1" : (hour >= 15 && hour < 23) ? "2" : "3";
        }

        const fieldNames = [
          'kode_beras',
          'berat',
          'kode_produksi',
          'basket',
          'gas',
          'waktu_masak',
          'suhu_produk',
          'suhu_after',
          'suhu_vacuum',
          'jam_mulai',
          'jam_selesai',
          'kematangan',
          'rasa',
          'aroma',
          'tekstur',
          'warna',
          'aksi'
      ];

      addColumnBtn.addEventListener('click', function () {
        columnIndex++;
        const newHeader = document.createElement('th');
        newHeader.textContent = `Pemeriksaan ${columnIndex}`;
        headerRow.appendChild(newHeader);

        const rows = riceTable.querySelectorAll('tbody tr');
        let rowIndex = 0; 

        rows.forEach((row) => {
            if (row.classList.contains('section-header')) {
                row.querySelector('td').setAttribute('colspan', columnIndex + 1);
            } else {
                const td = document.createElement('td');
                const field = fieldNames[rowIndex]; 

                if (field === 'gas') {
                    td.innerHTML = `
                      <select name="cooker[${columnIndex - 1}][gas]" class="form-control form-control-sm">
                        <option value="ON">ON</option>
                        <option value="OFF">OFF</option>
                    </select>`;
                } 
                else if (['jam_mulai','jam_selesai'].includes(field)) {
                    td.innerHTML = `<input type="time" name="cooker[${columnIndex - 1}][${field}]" class="form-control form-control-sm">`;
                } 
                else if (['basket','waktu_masak'].includes(field)) {
                    td.innerHTML = `<input type="number" name="cooker[${columnIndex - 1}][${field}]" class="form-control form-control-sm">`;
                } 
                else if (['kematangan','rasa','aroma','tekstur','warna'].includes(field)) {
                    td.innerHTML = `<input type="checkbox" name="cooker[${columnIndex - 1}][sensori][${field}]" value="Oke" class="big-checkbox">`;
                } 
                else if (field === 'aksi') {
                    td.innerHTML = `<button type="button" class="btn btn-danger btn-sm removeColumn">Hapus</button>`;
                } 
                else {
                    td.innerHTML = `<input type="text" name="cooker[${columnIndex - 1}][${field}]" class="form-control form-control-sm">`;
                }


                row.appendChild(td);
                rowIndex++;
            }
        });
    });

      riceTable.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeColumn')) {
            const colIndex = Array.from(e.target.closest('tr').children).indexOf(e.target.closest('td'));
            riceTable.querySelectorAll('tr').forEach(row => {
                if (row.children[colIndex]) {
                    row.removeChild(row.children[colIndex]);
                }
            });
            columnIndex--;
                // Update colspan setelah hapus
            riceTable.querySelectorAll('.section-header td').forEach(cell => {
                cell.setAttribute('colspan', columnIndex + 1);
            });
            headerRow.removeChild(headerRow.lastChild);
        }
    });
  });
</script>
@endsection

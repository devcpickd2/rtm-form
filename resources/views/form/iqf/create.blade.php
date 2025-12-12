@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Pengecekan Suhu Produk Setiap iqf Proses</h4>
            <form method="POST" action="{{ route('iqf.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" required>
                                @error('date')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                                @error('shift')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">IQF No.</label>
                                <input type="text" id="no_iqf" name="no_iqf" class="form-control">
                                @error('iqf')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $data->nama_produk ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('nama_produk')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" required>
                                @error('kode_produksi')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Std CT (°C)</label>
                                <select id="std_suhu" name="std_suhu" class="form-control" required>
                                    <option value="-18.0" selected>-18.0</option>
                                    <option value="-10.0">-10.0</option>
                                </select>
                                @error('std_suhu')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white text-center">
                        <strong>Suhu Pusat Produk (°C)</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0 text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Pukul</th>
                                    <th></th>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                    <th>5</th>
                                    <th>6</th>
                                    <th>7</th>
                                    <th>8</th>
                                    <th>9</th>
                                    <th>10</th>
                                    <th>X</th>
                                </tr>
                            </thead>

                            {{-- Input Jam Mulai --}}
                            <tbody>
                              <tr>
                                <td rowspan="2">
                                    <input type="time" name="pukul" class="form-control form-control-sm">
                                    @error('pukul')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>
                                <td>Suhu</td>
                                <td><input type="number" name="suhu_pusat[1][value]" class="form-control form-control-sm" step="0.1"></td>
                                <td><input type="number" name="suhu_pusat[2][value]" class="form-control form-control-sm" step="0.1"></td>
                                <td><input type="number" name="suhu_pusat[3][value]" class="form-control form-control-sm" step="0.1"></td>
                                <td><input type="number" name="suhu_pusat[4][value]" class="form-control form-control-sm" step="0.1"></td>
                                <td><input type="number" name="suhu_pusat[5][value]" class="form-control form-control-sm" step="0.1"></td>
                                <td><input type="number" name="suhu_pusat[6][value]" class="form-control form-control-sm" step="0.1"></td>
                                <td><input type="number" name="suhu_pusat[7][value]" class="form-control form-control-sm" step="0.1"></td>
                                <td><input type="number" name="suhu_pusat[8][value]" class="form-control form-control-sm" step="0.1"></td>
                                <td><input type="number" name="suhu_pusat[9][value]" class="form-control form-control-sm" step="0.1"></td>
                                <td><input type="number" name="suhu_pusat[10][value]" class="form-control form-control-sm" step="0.1"></td>
                                <td rowspan="2">
                                    <input type="number" name="average" class="form-control form-control-sm" step="0.01">
                                </td>
                            </tr>
                        </tbody>

                        <tbody>
                          <tr>
                            <td></td>
                            <td>Keterangan</td>
                            <td><input type="text" name="suhu_pusat[1][ket]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="suhu_pusat[2][ket]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="suhu_pusat[3][ket]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="suhu_pusat[4][ket]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="suhu_pusat[5][ket]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="suhu_pusat[6][ket]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="suhu_pusat[7][ket]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="suhu_pusat[8][ket]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="suhu_pusat[9][ket]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="suhu_pusat[10][ket]" class="form-control form-control-sm"></td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>

        {{-- Notes --}}
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong>Problem</strong>
            </div>
            <div class="card-body">
                <textarea name="problem" class="form-control" rows="3" placeholder="Tambahkan problem bila ada"></textarea>
            </div>
            <div class="card-header bg-light">
                <strong>Tindakan Koreksi</strong>
            </div>
            <div class="card-body">
                <textarea name="tindakan_koreksi" class="form-control" rows="3" placeholder="Tambahkan tindakan koreksi bila ada"></textarea>
            </div>
        </div>

        {{-- Notes --}}
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong>Catatan</strong>
            </div>
            <div class="card-body">
                <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada"></textarea>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success w-auto">
                <i class="bi bi-save"></i> Simpan
            </button>
            <a href="{{ route('iqf.index') }}" class="btn btn-secondary w-auto">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
</div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>
<style>
/* Supaya tabel lebih lebar */
.table {
  width: 100%;
  table-layout: auto; /* kolom auto */
}

.table th,
.table td {
  padding: 0.75rem 0.75rem; /* Lebih besar dari sebelumnya */
  vertical-align: middle;
  font-size: 0.9rem;
}

/* Kolom input kecil tapi fleksibel */
.table input.form-control-sm {
  width: 100%;
  min-width: 80px;
  font-size: 0.9rem;
}

/* Input group supaya nggak mepet */
.input-group-sm > .form-control,
.input-group-sm > .input-group-text {
  height: calc(2em + 0.5rem + 2px);
  font-size: 0.9rem;
}

/* Tabel kecil (nama bahan + suhu) supaya nyaman */
.table td table {
  width: 100%;
}

.table td table th,
.table td table td {
  padding: 0.5rem;
  font-size: 0.85rem;
}

/* Buat judul header lebih bold dan jelas */
.table thead th {
  background-color: #f8f9fa;
  font-weight: 600;
  text-align: center;
}
.table-sm th, .table-sm td {
  padding: 0.5rem;
  vertical-align: middle;
}
.input-group-sm>.form-control,
.input-group-sm>.input-group-text {
  height: calc(1.5em + 0.5rem + 2px);
  font-size: 0.875rem;
}
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const timeInput = document.querySelector('input[name="pukul"]'); 
        const shiftInput = document.getElementById("shiftInput");

        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');
        let min = String(now.getMinutes()).padStart(2, '0');

    // Set Tanggal
        dateInput.value = `${yyyy}-${mm}-${dd}`;

    // Set Jam (pukul)
        if (timeInput) {
            timeInput.value = `${hh}:${min}`;
        }

    // Tentukan Shift
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = [];
    for (let i = 1; i <= 10; i++) {    // SUDAH BENAR
        inputs[i] = document.querySelector(`input[name="suhu_pusat[${i}][value]"]`);
        inputs[i].addEventListener('input', calculateAverage);
    }

    const avgInput = document.querySelector('input[name="average"]');

    function calculateAverage() {
        let sum = 0;
        let count = 0;
        for (let i = 1; i <= 10; i++) {
            const val = parseFloat(inputs[i].value);
            if (!isNaN(val)) {
                sum += val;
                count++;
            }
        }
        avgInput.value = count > 0 ? (sum / count).toFixed(2) : '';
    }
});

</script>

@endsection

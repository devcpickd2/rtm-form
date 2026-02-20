@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Verifikasi Gramasi Topping</h4>
            <form method="POST" action="{{ route('gramasi.store') }}" enctype="multipart/form-data">
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
                        <div class="col-md-6">
                            <label class="form-label">Kode Produksi</label>
                            <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Pemeriksaan --}}
            <div class="card mb-3">
              <div class="card-header bg-info text-white">
                <strong>Pemeriksaan Gramasi</strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-bordered table-sm mb-0 text-center align-middle">

                    {{-- Header Jam Mulai --}}
                    <thead class="table-light">
                      <tr>
                        <th colspan="8">Berat Topping Aktual (gram)</th>
                    </tr>
                    <tr>
                        <th>Jenis Topping</th>
                        <th>Standar (gram)</th>
                        <th>Pukul</th>
                        <th>Gramasi</th>
                        <th>Pukul</th>
                        <th>Gramasi</th>
                        <th>Pukul</th>
                        <th>Gramasi</th>
                    </tr>
                </thead>

                <tbody>
                    @for ($i = 0; $i < 6; $i++)
                    <tr>
                        <td>
                            <input type="text" name="jenis_topping[{{ $i }}]" class="form-control form-control-sm">
                        </td>
                        <td>
                            <input type="number" name="standar[{{ $i }}]" class="form-control form-control-sm" step="0.1">
                        </td>
                        @if($i == 0)
                        {{-- Pukul & Gramasi hanya input 1 kali --}}
                        <td rowspan="6">
                            <input type="time" name="pukul_1" class="form-control form-control-sm">
                        </td>
                        @endif
                        <td>
                            <input type="number" name="gramasi_1[{{ $i }}]" class="form-control form-control-sm" step="0.1">
                        </td>
                        @if($i == 0)
                        {{-- Pukul & Gramasi hanya input 1 kali --}}
                        <td rowspan="6">
                            <input type="time" name="pukul_2" class="form-control form-control-sm">
                        </td>
                        @endif
                        <td>
                            <input type="number" name="gramasi_2[{{ $i }}]" class="form-control form-control-sm" step="0.1">
                        </td>
                        @if($i == 0)
                        {{-- Pukul & Gramasi hanya input 1 kali --}}
                        <td rowspan="6">
                            <input type="time" name="pukul_3" class="form-control form-control-sm">
                        </td>
                        @endif
                        <td>
                            <input type="number" name="gramasi_3[{{ $i }}]" class="form-control form-control-sm" step="0.1">
                        </td>

                    </tr>
                    @endfor
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
        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada"></textarea>
    </div>
</div>

{{-- Tombol --}}
<div class="d-flex justify-content-between mt-3">
    <button class="btn btn-success w-auto">
        <i class="bi bi-save"></i> Simpan
    </button>
    <a href="{{ route('gramasi.index') }}" class="btn btn-secondary w-auto">
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

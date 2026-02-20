@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Pemantauan Suhu Produk di Cold Storage</h4>
            <form method="POST" action="{{ route('cold_storage.update',$cold_storage->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" value="{{ $cold_storage->date }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ $cold_storage->shift==1?'selected':'' }}>Shift 1</option>
                                    <option value="2" {{ $cold_storage->shift==2?'selected':'' }}>Shift 2</option>
                                    <option value="3" {{ $cold_storage->shift==3?'selected':'' }}>Shift 3</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pukul</label>
                                <input type="time" id="timeInput" name="pukul" class="form-control" value="{{ $cold_storage->pukul }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <strong>Pemeriksaan Suhu Produk</strong>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0 text-center align-middle">
                                <thead>
                                    <tr class="table-primary">
                                        <th colspan="10" class="fw-bold text-uppercase"><b>Cold Storage / Finish Good</b></th>
                                    </tr>
                                    <tr class="table-light">
                                        <th rowspan="2" class="col-nama">Nama Produk</th>
                                        <th rowspan="2" class="col-kode">Kode Produksi</th>
                                        <th class="col-standar">Suhu Cold Storage</th>
                                        <th colspan="6">Suhu Produk di Cold Storage (°C)</th>
                                        <th rowspan="2" class="col-ket">Keterangan</th>
                                    </tr>
                                    <tr class="table-light">
                                        <th class="col-standar">(°C)</th>
                                        <th class="col-cek">1</th>
                                        <th class="col-cek">2</th>
                                        <th class="col-cek">3</th>
                                        <th class="col-cek">4</th>
                                        <th class="col-cek">5</th>
                                        <th class="col-cek">Rata2</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($suhuData as $i => $row)
                                    <tr>
                                        <td class="col-nama">
                                            <select name="suhu_cs[{{ $i }}][nama_produk]" 
                                            class="form-select form-select-sm selectpicker" 
                                            data-live-search="true" title="Pilih Produk">
                                            @foreach($produks as $produk)
                                            <option value="{{ $produk->nama_produk }}" 
                                                {{ $row['nama_produk']==$produk->nama_produk?'selected':'' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="col-kode">
                                        <input type="text" name="suhu_cs[{{ $i }}][kode_produksi]" 
                                        class="form-control form-control-sm" 
                                        value="{{ $row['kode_produksi'] }}">
                                    </td>

                                    <!-- Suhu Standar -->
                                    <td class="col-standar">
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary minus-btn" type="button">−</button>
                                            <input type="number" 
                                            name="suhu_cs[{{ $i }}][suhu_standar]" 
                                            class="form-control form-control-sm"
                                            step="0.1"
                                            value="{{ $row['suhu_standar'] }}">
                                        </div>
                                    </td>

                                    <!-- Cek 1-5 -->
                                    @for ($j = 1; $j <= 5; $j++)
                                    <td class="col-cek">
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary minus-btn" type="button">−</button>
                                            <input type="number" 
                                            name="suhu_cs[{{ $i }}][cek_{{ $j }}]" 
                                            class="form-control form-control-sm cek-input" 
                                            step="0.1" 
                                            data-index="{{ $i }}" 
                                            value="{{ $row['cek_'.$j] }}">
                                        </div>
                                    </td>
                                    @endfor

                                    <!-- Rata-rata -->
                                    <td class="col-cek">
                                        <input type="number" 
                                        name="suhu_cs[{{ $i }}][rata_rata]" 
                                        class="form-control form-control-sm rata-input" 
                                        step="0.1" data-index="{{ $i }}" 
                                        value="{{ $row['rata_rata'] }}" readonly>
                                    </td>

                                    <!-- Keterangan -->
                                    <td class="col-ket">
                                        <input type="text" name="suhu_cs[{{ $i }}][keterangan]" 
                                        class="form-control form-control-sm" 
                                        value="{{ $row['keterangan'] }}">
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
                    <textarea name="catatan" class="form-control" rows="3">{{ $cold_storage->catatan }}</textarea>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>Warehouse</strong>
                </div>

                <div class="card-body">
                    <label class="form-label">Nama Warehouse</label>
                    <select id="nama_warehouse" name="nama_warehouse" class="form-control" required>
                        <option value="">--Pilih Warehouse--</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->nama_karyawan }}"
                            {{ old('nama_warehouse', $cold_storage->nama_warehouse) == $wh->nama_karyawan ? 'selected' : '' }}>
                            {{ $wh->nama_karyawan }}
                        </option>
                        @endforeach

                    </select>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-success w-auto">
                    <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('cold_storage.index') }}" class="btn btn-secondary w-auto">
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
/* Scroll horizontal */
.table-responsive {
  overflow-x: auto;
}

/* Umum */
.table {
  width: 100%;
  table-layout: auto;
}

.table th,
.table td {
  padding: 0.75rem;
  vertical-align: middle;
  font-size: 0.9rem;
  text-align: center;
}

/* Select/Input full width */
.table td select.form-select,
.table td input.form-control-sm {
  width: 100%;
  font-size: 0.9rem;
}

/* === Lebar Kolom === */
.col-nama {
  min-width: 300px; /* Nama Produk paling lebar */
}

.col-kode {
  min-width: 250px; /* Kode Produksi lebar */
}

.col-standar {
    min-width: 150px; /* Standar Suhu kecil */
}

.col-cek {
  min-width: 100px; /* Cek suhu & Rata2 kecil */
}

.col-ket {
  min-width: 200px; /* Keterangan lebar lagi */
}

/* Header */
.table thead th {
  background-color: #f8f9fa;
  font-weight: 600;
}

/* Selectpicker full width */
.bootstrap-select .dropdown-toggle {
  width: 100% !important;
}
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
  // tombol minus toggle tanda minus di input sebelahnya
      document.querySelectorAll('.minus-btn').forEach(btn => {
        btn.addEventListener('click', function () {
          let input = this.nextElementSibling; 
          if (!input.value) {
            input.value = "-"; 
        } else if (input.value.startsWith("-")) {
            input.value = input.value.substring(1);
        } else {
            input.value = "-" + input.value;
        }
        input.focus();
    });
    });

  // hitung rata-rata otomatis cek_1 s/d cek_5
      document.querySelectorAll('.cek-input').forEach(function(input){
        input.addEventListener('input', function(){
          let idx = this.dataset.index;
          let cekValues = [];
          document.querySelectorAll('.cek-input[data-index="'+idx+'"]').forEach(function(cek){
            let val = parseFloat(cek.value);
            if(!isNaN(val)) cekValues.push(val);
        });
          let rataInput = document.querySelector('.rata-input[data-index="'+idx+'"]');
          if (cekValues.length > 0) {
            let sum = cekValues.reduce((a,b)=>a+b,0);
            rataInput.value = (sum / cekValues.length).toFixed(1);
        } else {
            rataInput.value = '';
        }
    });
    });
  });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    // hitung rata-rata cek_1 s/d cek_5 otomatis
        document.querySelectorAll('.cek-input').forEach(function(input){
            input.addEventListener('input', function(){
            let idx = this.dataset.index; // ambil index baris
            let cekValues = [];
            document.querySelectorAll('.cek-input[data-index="'+idx+'"]').forEach(function(cek){
                let val = parseFloat(cek.value);
                if(!isNaN(val)) cekValues.push(val);
            });
            if (cekValues.length > 0) {
                let sum = cekValues.reduce((a,b)=>a+b,0);
                let avg = sum / cekValues.length;
                let rataInput = document.querySelector('.rata-input[data-index="'+idx+'"]');
                if(rataInput) rataInput.value = avg.toFixed(1); // 1 angka desimal
            } else {
                let rataInput = document.querySelector('.rata-input[data-index="'+idx+'"]');
                if(rataInput) rataInput.value = '';
            }
        });
        });
    });
</script>
@endsection

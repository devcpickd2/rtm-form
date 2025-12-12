@extends('layouts.app')

@section('content')
<!-- jQuery dulu (wajib) -->

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Pemasakan Produk di Steam/Cooking Kettle</h4>
            <form method="POST" action="{{ route('cooking.store') }}" enctype="multipart/form-data">
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
                            <div class="col-md-6">
                                <label class="form-label">Sub Produk</label>
                                <select id="sub_produk" name="sub_produk" class="form-control">
                                    <option value="">-- Pilih Sub Produk --</option>
                                    <option value="Saus">Saus</option>
                                    <option value="Daging">Daging</option>
                                    <option value="Sambal Hijau">Sambal Hijau</option>
                                    <option value="Daun Singkong">Daun Singkong</option>
                                    <option value="Kentang Balado">Kentang Balado</option>
                                    <option value="Sambel Merah">Sambel Merah</option>
                                    <option value="Toping Ayam Jamur">Toping Ayam Jamur</option>
                                    <option value="Saus Kecap">Saus Kecap</option>
                                    <option value="Minyak Bawang">Minyak Bawang</option>
                                    <option value="Bawang Putih Sauted">Bawang Putih Sauted</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Produk</label>
                                <select id="jenis_produk" name="jenis_produk" class="form-control" required>
                                    <option value="">-- Pilih Jenis Produk --</option>
                                    <option value="RTS">RTS (Ready to Serve)</option>
                                    <option value="RTM">RTM (Ready to Meal)</option>
                                    <option value="Institusi">Institusi</option>
                                    <option value="Yoshinoya">Yoshinoya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Waktu Proses</label>
                                <div class="input-group">
                                    <input type="time" id="waktu_mulai" name="waktu_mulai" class="form-control">
                                    <span class="input-group-text">s/d</span>
                                    <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                              <label class="form-label">Mesin</label>
                              <select id="nama_mesin" name="nama_mesin[]" 
                              class="selectpicker" 
                              multiple 
                              data-live-search="true"
                              title="-- Pilih Nama Mesin --"
                              data-width="100%">
                              <option value="Provisur">Provisur</option>
                              <option value="Kettle Api">Kettle Api</option>
                              <option value="Kettle Steam">Kettle Steam</option>
                              <option value="Kettle Api XCG 300">Kettle Api XCG 300</option>
                              <option value="Alco">Alco</option>
                          </select>
                      </div>

                  </div>
              </div>
          </div>

          {{-- Bagian Pemeriksaan Cooking --}}
          <div class="card mb-4">
            <div class="card-body">

                {{-- Catatan Checkbox --}}
                <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle"></i>
                    <strong>Catatan:</strong>  
                    <i class="bi bi-check-circle text-success"></i> Checkbox apabila hasil <u>Oke</u>.  
                    Kosongkan Checkbox apabila hasil <u>Tidak Oke</u>.  
                </div>

                <div class="table-responsive">
                    <table id="cookingTable">
                       <thead class="table-light align-middle">
                        <tr>
                            <th rowspan="2">Pukul</th>
                            <th rowspan="2">Tahapan Proses</th>
                            <th colspan="4">Bahan Baku</th>
                            <th colspan="7">Parameter Pemasakan</th>
                            <th colspan="6">Produk</th>
                            <th rowspan="2">Catatan</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <!-- Bahan Baku -->
                            <th>Jenis Bahan</th>
                            <th>Kode Bahan</th>
                            <th>Jumlah Standar (Kg)</th>
                            <th>Jumlah Aktual (Kg)</th>

                            <!-- Parameter Pemasakan -->
                            <th>Sensori</th>
                            <th>Lama Proses<br>(menit)</th>
                            <th>Mixing Paddle On</th>
                            <th>Mixing Paddle Off</th>
                            <th>Pressure (Bar)</th>
                            <th>Temperature (°C / Api)</th>
                            <th>Target Temp (°C)</th>
                            <th>Actual Temp (°C)</th>

                            <!-- Produk -->
                            <th>Suhu Pusat Produk<br>Setelah 1/30* Menit (°C)</th>
                            <th>Warna</th>
                            <th>Aroma</th>
                            <th>Rasa</th>
                            <th>Tekstur</th>
                        </tr>
                    </thead>

                    <tbody class="pemeriksaan">
                        @for($i=1; $i<=10; $i++)
                        <tr>
                            @if($i==1)
                            <td rowspan="10"><input type="time" name="pemasakan[0][pukul]" class="form-control form-control-sm"></td>
                            <td rowspan="10"><input type="text" name="pemasakan[0][tahapan]" class="form-control form-control-sm"></td>
                            @endif

                            <td><input type="text" name="pemasakan[0][jenis_bahan][]" class="form-control form-control-sm"></td>
                            <td><input type="text" name="pemasakan[0][kode_bahan][]" class="form-control form-control-sm"></td>
                            <td><input type="number" step="0.01" name="pemasakan[0][jumlah_standar][]" class="form-control form-control-sm"></td>
                            <td><input type="number" step="0.01" name="pemasakan[0][jumlah_aktual][]" class="form-control form-control-sm"></td>
                            <td>
                              <input type="checkbox" class="big-checkbox" 
                              name="pemasakan[0][sensori][]" 
                              value="Oke" 
                              {{ !empty($p['sensori'][$i]) && $p['sensori'][$i]=='Oke' ? 'checked' : '' }}>
                          </td>

                          @if($i==1)
                          <td rowspan="10"><input type="number" step="0.01" name="pemasakan[0][lama_proses]" class="form-control form-control-sm"></td>
                          <td rowspan="10"><input type="checkbox" class="big-checkbox" name="pemasakan[0][paddle_on]" value="1"></td>
                          <td rowspan="10"><input type="checkbox" class="big-checkbox" name="pemasakan[0][paddle_off]" value="1"></td>
                          <td rowspan="10"><input type="number" step="0.01" name="pemasakan[0][pressure]" class="form-control form-control-sm"></td>
                          <td rowspan="10"><input type="text" name="pemasakan[0][temperature]" class="form-control form-control-sm"></td>
                          <td rowspan="10"><input type="number" step="0.01" name="pemasakan[0][target_temp]" class="form-control form-control-sm"></td>
                          <td rowspan="10"><input type="number" step="0.01" name="pemasakan[0][actual_temp]" class="form-control form-control-sm"></td>
                          <td rowspan="10">
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.01" name="pemasakan[0][suhu_pusat]" class="form-control">
                                <select name="pemasakan[0][suhu_pusat_menit]" class="form-select">
                                    <option value="">Pilih Menit</option>
                                    <option value="1">1 Menit</option>
                                    <option value="30">30 Menit</option>
                                </select>
                            </div>
                        </td>
                        <td rowspan="10"><input type="checkbox" class="big-checkbox" name="pemasakan[0][warna]" value="Oke"></td>
                        <td rowspan="10"><input type="checkbox" class="big-checkbox" name="pemasakan[0][aroma]" value="Oke"></td>
                        <td rowspan="10"><input type="checkbox" class="big-checkbox" name="pemasakan[0][rasa]" value="Oke"></td>
                        <td rowspan="10"><input type="checkbox" class="big-checkbox" name="pemasakan[0][tekstur]" value="Oke"></td>
                        <td rowspan="10"><input type="text" name="pemasakan[0][catatan]" class="form-control form-control-sm"></td>
                        <td rowspan="10">
                            {{-- Tombol hapus, disembunyikan untuk pemeriksaan pertama --}}
                            <button type="button" class="btn btn-danger btn-sm btn-hapus d-none">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        {{-- Tombol tambah --}}
        <button type="button" class="btn btn-primary btn-sm mt-2" id="btnTambahPemeriksaan">
            <i class="bi bi-plus-circle"></i> Tambah Pemeriksaan
        </button>
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
    <a href="{{ route('cooking.index') }}" class="btn btn-secondary w-auto">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
</form>
</div>
</div>
</div>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

<style>
    /* Checkbox besar */
    .big-checkbox {
        width: 20px;
        height: 20px;
        transform: scale(1.5);  /* perbesar */
        cursor: pointer;
        margin: 3px;           /* kasih jarak biar ga dempet */
    }

    .table-responsive {
        overflow-x: auto;   /* bikin scroll horizontal */
        -webkit-overflow-scrolling: touch;
    }

    #cookingTable {
        border-collapse: collapse;
        width: 100%;
        min-width: 1800px;   /* kasih minimal lebar biar gak dipaksa sempit */
        table-layout: auto;  /* biarkan otomatis sesuai isi */
    }

    #cookingTable th, 
    #cookingTable td {
        border: 1px solid #dee2e6;
        padding: 6px;
        vertical-align: middle;
        text-align: center;
        font-size: 0.9rem;
        white-space: nowrap; /* jangan patah kata */
    }

    #cookingTable thead th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        position: sticky;
        top: 0;
        z-index: 3;
    }

    /* Striping */
    #cookingTable tbody tr:nth-child(odd) {
        background-color: #f9f9f9;
    }
    #cookingTable tbody tr:hover {
        background-color: #e9f7fe;
    }

    /* Input & select */
    .form-control-sm, 
    .form-select {
        width: 100%;
        min-width: 100px;
        max-width: 160px;
        padding: 2px 4px;
        font-size: 0.85rem;
    }

    .big-checkbox {
        width: 18px;
        height: 18px;
        transform: scale(1.3);
        cursor: pointer;
        margin: auto;
        display: block;
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
<script>
    document.getElementById('btnTambahPemeriksaan').addEventListener('click', function() {
        let table = document.getElementById('cookingTable');
        let lastTbody = table.querySelector('tbody.pemeriksaan:last-of-type');
        let clone = lastTbody.cloneNode(true);

    // hitung index baru
        let index = table.querySelectorAll('tbody.pemeriksaan').length;

    // update semua input name + kosongkan value
        clone.querySelectorAll('input, select').forEach(function(el) {
            if (el.name) {
                el.name = el.name.replace(/\[\d+\]/, '['+index+']');
                if (el.type === 'checkbox') {
                    el.checked = false;
                } else {
                    el.value = '';
                }
            }
        });

    // tampilkan tombol hapus pada blok clone
        let btnHapus = clone.querySelector('.btn-hapus');
        btnHapus.classList.remove('d-none');
        btnHapus.addEventListener('click', function() {
            clone.remove();
        });

    // tempelkan di bawah
        table.appendChild(clone);
    });
</script>
@endsection
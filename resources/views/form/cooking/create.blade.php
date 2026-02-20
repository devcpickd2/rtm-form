@extends('layouts.app')

@section('content')
<!-- jQuery dulu (wajib) -->

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
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
                                <input type="date"
                                id="dateInput"
                                name="date"
                                class="form-control @error('date') is-invalid @enderror"
                                value="{{ old('date', $data->date ?? '') }}"
                                required>

                                @error('date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput"
                                name="shift"
                                class="form-control @error('shift') is-invalid @enderror"
                                required>
                                <option value="1" {{ old('shift', $data->shift ?? '') == '1' ? 'selected' : '' }}>Shift 1</option>
                                <option value="2" {{ old('shift', $data->shift ?? '') == '2' ? 'selected' : '' }}>Shift 2</option>
                                <option value="3" {{ old('shift', $data->shift ?? '') == '3' ? 'selected' : '' }}>Shift 3</option>
                            </select>

                            @error('shift')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                       <div class="col-md-6">
                        <label class="form-label">Nama Produk</label>
                        <select id="nama_produk" name="nama_produk"
                        class="form-control selectpicker"
                        data-live-search="true"
                        title="Ketik nama produk..."
                        required>

                        @foreach($produks as $produk)
                        <option value="{{ $produk->nama_produk }}"
                            data-spesifikasi='@json(
                            is_string($produk->spesifikasi)
                            ? json_decode($produk->spesifikasi, true)
                            : $produk->spesifikasi
                            )'
                            {{ old('nama_produk', $data->nama_produk ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                            {{ $produk->nama_produk }}
                        </option>
                        @endforeach

                    </select>

                    @error('nama_produk')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sub Produk</label>
                    <select id="sub_produk" name="sub_produk"  class="form-control @error('sub_produk') is-invalid @enderror">
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
                        <option value="Bawang Sauted">Bawang Sauted</option>
                        <option value="Onion Topping"> Onion Topping</option>
                        <option value="Air Asam Jawa">Air Asam Jawa</option>
                        <option value="Daun Bawang">Daun Bawang</option>
                    </select>
                    @error('sub_produk')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Jenis Produk</label>
                    <select id="jenis_produk" name="jenis_produk" class="form-control @error('jenis_produk') is-invalid @enderror" required>
                        <option value="">-- Pilih Jenis Produk --</option>
                        <option value="RTS">RTS (Ready to Serve)</option>
                        <option value="RTM">RTM (Ready to Meal)</option>
                        <option value="Institusi">Institusi</option>
                        <option value="Yoshinoya">Yoshinoya</option>
                        <option value="Pizza">Pizza</option>
                    </select>
                    @error('jenis_produk')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kode Produksi</label>
                    <input type="text"
                    id="kode_produksi"
                    name="kode_produksi"
                    class="form-control @error('kode_produksi') is-invalid @enderror"
                    required>

                    @error('kode_produksi')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Waktu Proses</label>
                    <div class="input-group">
                     <input type="time"
                     name="waktu_mulai"
                     class="form-control @error('waktu_mulai') is-invalid @enderror">
                     @error('waktu_mulai')
                     <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                    <span class="input-group-text">s/d</span>
                    <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control">
                </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Mesin</label>
              <div class="@error('nama_mesin') is-invalid @enderror">
                <select id="nama_mesin"
                name="nama_mesin[]"
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
        @error('nama_mesin')
        <div class="text-danger small mt-1">
            {{ $message }}
        </div>
        @enderror
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
                    <th colspan="6">Bahan Baku</th>
                    <th colspan="8">Parameter Pemasakan</th>
                    <th colspan="4">Produk</th>
                    <th rowspan="2">Catatan</th>
                    <th rowspan="2">Action</th>
                </tr>
                <tr>
                    <!-- Bahan Baku -->
                    <th>Jenis Bahan</th>
                    <th>Kode Bahan</th>
                    <th>Jumlah Standar (Kg)</th>
                    <th>Jumlah Aktual (Kg)</th>
                    <th>Sensori</th>
                    <th>Action</th>
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

            <tbody class="pemeriksaan" data-index="0">
                <tr class="bahan-row">
                    {{-- KOLOM TETAP --}}
                    <td class="rs-pukul" rowspan="1">
                        <input type="time" name="pemasakan[0][pukul]" class="form-control form-control-sm">
                    </td>

                    <td class="rs-tahapan" rowspan="1">
                        <input type="text" name="pemasakan[0][tahapan]" class="form-control form-control-sm">
                    </td>

                    {{-- === BAHAN BAKU (DINAMIS) === --}}
                    <td>
                        <input type="text" name="pemasakan[0][jenis_bahan][]" class="form-control form-control-sm">
                    </td>
                    <td>
                        <input type="text" name="pemasakan[0][kode_bahan][]" class="form-control form-control-sm">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="pemasakan[0][jumlah_standar][]" class="form-control form-control-sm">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="pemasakan[0][jumlah_aktual][]" class="form-control form-control-sm">
                    </td>

                    <td class="text-center">
                        <input type="checkbox" class="big-checkbox" name="pemasakan[0][sensori][]" value="Oke">
                    </td>
                    {{-- ACTION --}}
                    <td class="rs-action" rowspan="1">
                        <button type="button" class="btn btn-success btn-sm btn-tambah-bahan">
                            <i class="bi bi-plus"></i>
                        </button>
                    </td>

                    {{-- === KOLOM SETELAHNYA (TETAP, TIDAK DIUBAH) === --}}
                    <td class="rs-parameter" rowspan="1">
                        <input type="number" step="0.01" name="pemasakan[0][lama_proses]" class="form-control form-control-sm">
                    </td>
                    <td class="rs-parameter" rowspan="1">
                        <input type="checkbox" class="big-checkbox" name="pemasakan[0][paddle_on]" value="1">
                    </td>
                    <td class="rs-parameter" rowspan="1">
                        <input type="checkbox" class="big-checkbox" name="pemasakan[0][paddle_off]" value="1">
                    </td>
                    <td class="rs-parameter" rowspan="1">
                        <input type="number" step="0.01" name="pemasakan[0][pressure]" class="form-control form-control-sm">
                    </td>
                    <td class="rs-parameter" rowspan="1">
                        <input type="text" name="pemasakan[0][temperature]" class="form-control form-control-sm">
                    </td>
                    <td class="rs-parameter" rowspan="1">
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" name="pemasakan[0][target_temp]" class="form-control form-control-sm">
                            <select name="pemasakan[0][target_temp_operator]" class="form-select">
                                <option value="">-</option>
                                <option value="≥">&ge;</option>
                                <option value="≤">&le;</option>
                            </select>
                        </div>
                    </td>

                    <td class="rs-parameter" rowspan="1">
                        <input type="number" step="0.01" name="pemasakan[0][actual_temp]" class="form-control form-control-sm">
                    </td>

                    <td class="rs-produk" rowspan="1">
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" name="pemasakan[0][suhu_pusat]" class="form-control">
                            <select name="pemasakan[0][suhu_pusat_menit]" class="form-select">
                                <option value="">Pilih Menit</option>
                                <option value="1">1 Menit</option>
                                <option value="30">30 Menit</option>
                            </select>
                        </div> 
                    </td>
                    <td class="rs-produk" rowspan="1"><input type="checkbox" class="big-checkbox" name="pemasakan[0][warna]" value="Oke"></td>
                    <td class="rs-produk" rowspan="1"><input type="checkbox" class="big-checkbox" name="pemasakan[0][aroma]" value="Oke"></td>
                    <td class="rs-produk" rowspan="1"><input type="checkbox" class="big-checkbox" name="pemasakan[0][rasa]" value="Oke"></td>
                    <td class="rs-produk" rowspan="1"><input type="checkbox" class="big-checkbox" name="pemasakan[0][tekstur]" value="Oke"></td>

                    <td class="rs-catatan" rowspan="1">
                        <textarea
                        name="pemasakan[0][catatan]"
                        class="form-control form-control-sm"
                        rows="6"
                        style="resize: vertical;"
                        placeholder="Catatan..."></textarea>
                    </td>
                    <td class="rs-action-pemeriksaan" rowspan="1">
                        <div class="action-center">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-pemeriksaan">
                                <i class="bi bi-trash"></i> Hapus Pemeriksaan
                            </button>
                        </div>
                    </td>
                </tr>
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
    document.addEventListener("DOMContentLoaded", function () {
        function updateRowspan(tbody) {
            let total = tbody.querySelectorAll('tr.bahan-row').length;
            tbody.querySelectorAll(
                '.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action'
                ).forEach(td => td.rowSpan = total);
        }

        function getSelectedSubProduk() {
            const el = document.getElementById('sub_produk');
            return el ? el.value.trim() : '';
        }

        function filterSpesifikasiBySubProduk(spesifikasi, subProduk) {
            if (!subProduk) return spesifikasi;
            return spesifikasi.filter(item =>
                !item.sub_produk || item.sub_produk === subProduk
                );
        }

        function renderSpesifikasi() {

            const produkSelect = document.getElementById('nama_produk');
            const table = document.getElementById('cookingTable');

    // ❗ JANGAN return, karena kita tetap mau munculin tabel
            if (!produkSelect || !produkSelect.selectedOptions.length) return;

    // 🔥 HAPUS SEMUA PEMERIKSAAN LAMA
            table.querySelectorAll('tbody.pemeriksaan').forEach(tb => tb.remove());

            const selected = produkSelect.selectedOptions[0];

            let spesifikasi = [];
            try {
                spesifikasi = JSON.parse(selected.dataset.spesifikasi || '[]');
            } catch (e) {
                spesifikasi = [];
            }

            const subProduk = getSelectedSubProduk();

    // FILTER SUB PRODUK
            if (Array.isArray(spesifikasi)) {
                spesifikasi = filterSpesifikasiBySubProduk(spesifikasi, subProduk);
            }

    // ===============================
    // 🔥 POIN PALING PENTING
    // ===============================
    // KALAU TIDAK ADA SPESIFIKASI → PAKSA 1 PEMERIKSAAN KOSONG
            if (!Array.isArray(spesifikasi) || spesifikasi.length === 0) {
                spesifikasi = [{
                    tahapan: '',
                    bahan: [{
                        nama: '',
                        berat: ''
                    }]
                }];
            }

    // ===============================
    // RENDER TABEL (PASTI JALAN)
    // ===============================
            spesifikasi.forEach((tahapanData, index) => {

                const tbody = document.createElement('tbody');
                tbody.classList.add('pemeriksaan');
                tbody.dataset.index = index;

                const bahanArr = Array.isArray(tahapanData.bahan) && tahapanData.bahan.length
                ? tahapanData.bahan
                : [{ nama: '', berat: '' }];

                bahanArr.forEach((bahan, i) => {

                    const row = document.createElement('tr');
                    row.classList.add('bahan-row');

                    row.innerHTML = `
                        ${i === 0 ? `
                <td class="rs-pukul">
                    <input type="time" name="pemasakan[${index}][pukul]" class="form-control form-control-sm">
                </td>
                <td class="rs-tahapan">
                    <input type="text" name="pemasakan[${index}][tahapan]"
                        class="form-control form-control-sm"
                        value="${tahapanData.tahapan || ''}">
                </td>
                            ` : ''}

                <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm" value="${bahan.nama || ''}"></td>
                <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm" value="${bahan.berat || ''}"></td>
                <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
                <td class="text-center">
                    <input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke">
                </td>

                            ${i === 0 ? `
                <td class="rs-action">
                    <button type="button" class="btn btn-success btn-sm btn-tambah-bahan">
                        <i class="bi bi-plus"></i>
                    </button>
                </td>

             <td class="rs-parameter">
    <input type="number" step="0.01"
        name="pemasakan[${index}][lama_proses]"
        class="form-control form-control-sm">
</td>

<td class="rs-parameter">
    <input type="checkbox" class="big-checkbox"
        name="pemasakan[${index}][paddle_on]" value="1">
</td>

<td class="rs-parameter">
    <input type="checkbox" class="big-checkbox"
        name="pemasakan[${index}][paddle_off]" value="1">
</td>

<td class="rs-parameter">
    <input type="number" step="0.01"
        name="pemasakan[${index}][pressure]"
        class="form-control form-control-sm">
</td>

<td class="rs-parameter">
    <input type="text"
        name="pemasakan[${index}][temperature]"
        class="form-control form-control-sm">
</td>

<td class="rs-parameter">
    <div class="input-group input-group-sm">
        <input type="number" step="0.01"
            name="pemasakan[${index}][target_temp]"
            class="form-control form-control-sm">
        <select name="pemasakan[${index}][target_temp_operator]"
            class="form-select">
            <option value="">-</option>
            <option value="≥">&ge;</option>
            <option value="≤">&le;</option>
        </select>
    </div>
</td>

<td class="rs-parameter">
    <input type="number" step="0.01"
        name="pemasakan[${index}][actual_temp]"
        class="form-control form-control-sm">
</td>
<td class="rs-produk">
    <div class="input-group input-group-sm">
        <input type="number" step="0.01"
            name="pemasakan[${index}][suhu_pusat]"
            class="form-control">
        <select name="pemasakan[${index}][suhu_pusat_menit]"
            class="form-select">
            <option value="">Pilih Menit</option>
            <option value="1">1 Menit</option>
            <option value="30">30 Menit</option>
        </select>
    </div>
</td>

                <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][warna]" value="Oke"></td>
                <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][aroma]" value="Oke"></td>
                <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][rasa]" value="Oke"></td>
                <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][tekstur]" value="Oke"></td>

                <td class="rs-catatan"><textarea name="pemasakan[${index}][catatan]" class="form-control form-control-sm"></textarea></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-hapus-pemeriksaan">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
                                ` : ''}
                            `;

                            tbody.appendChild(row);
                        });

updateRowspan(tbody);
table.appendChild(tbody);
});
}
document.getElementById('nama_produk').addEventListener('change', renderSpesifikasi);
document.getElementById('sub_produk').addEventListener('change', renderSpesifikasi);

document.addEventListener('click', function (e) {

    if (e.target.closest('.btn-tambah-bahan')) {
        let tbody = e.target.closest('tbody.pemeriksaan');
        let index = tbody.dataset.index;

        let row = document.createElement('tr');
        row.classList.add('bahan-row');
        row.innerHTML = `
                <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="number" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm" step="0.01"></td>
                <td><input type="number" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm" step="0.01"></td>
                <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>
                <td></td>
        `;
        tbody.appendChild(row);
        updateRowspan(tbody);
    }

    if (e.target.closest('.btn-hapus-pemeriksaan')) {
        let tbody = e.target.closest('tbody.pemeriksaan');
        if (document.querySelectorAll('tbody.pemeriksaan').length > 1) {
            tbody.remove();
        } else {
            alert('Minimal ada 1 pemeriksaan.');
        }
    }
});

document.getElementById('btnTambahPemeriksaan').addEventListener('click', function () {

    let table = document.getElementById('cookingTable');
    let lastTbody = table.querySelector('tbody.pemeriksaan:last-of-type');
    let clone = lastTbody.cloneNode(true);

    clone.querySelectorAll('tr.bahan-row').forEach((r,i)=>{ if(i>0) r.remove(); });

    let index = table.querySelectorAll('tbody.pemeriksaan').length;
    clone.dataset.index = index;

    clone.querySelectorAll('input, textarea').forEach(el => {
        if(el.name) el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
        el.type === 'checkbox' ? el.checked = false : el.value = '';
    });

    clone.querySelectorAll('.rs-pukul,.rs-tahapan,.rs-parameter,.rs-produk,.rs-catatan,.rs-action')
    .forEach(td => td.rowSpan = 1);

    lastTbody.after(clone);
    updateRowspan(clone);
});

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

@endsection
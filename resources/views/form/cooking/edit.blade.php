@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Pemasakan Produk di Steam/Cooking Kettle</h4>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('cooking.update', $cooking->uuid) }}">
                @csrf
                @method('PUT')

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
                                value="{{ old('date', $cooking->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $cooking->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $cooking->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $cooking->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $cooking->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sub Produk</label>
                                <select id="sub_produk" name="sub_produk" class="form-control">
                                    <option value="">-- Pilih Sub Produk --</option>
                                    <option value="Saus" {{ old('sub_produk', $cooking->sub_produk) == 'Saus' ? 'selected' : '' }}>Saus</option>
                                    <option value="Daging" {{ old('sub_produk', $cooking->sub_produk) == 'Daging' ? 'selected' : '' }}>Daging</option>
                                    <option value="Sambal Hijau" {{ old('sub_produk', $cooking->sub_produk) == 'Sambal Hijau' ? 'selected' : '' }}>Sambal Hijau</option>
                                    <option value="Daun Singkong" {{ old('sub_produk', $cooking->sub_produk) == 'Daun Singkong' ? 'selected' : '' }}>Daun Singkong</option>
                                    <option value="Kentang Balado" {{ old('sub_produk', $cooking->sub_produk) == 'Kentang Balado' ? 'selected' : '' }}>Kentang Balado</option>
                                    <option value="Sambel Merah" {{ old('sub_produk', $cooking->sub_produk) == 'Sambel Merah' ? 'selected' : '' }}>Sambel Merah</option>
                                    <option value="Toping Ayam Jamur" {{ old('sub_produk', $cooking->sub_produk) == 'Toping Ayam Jamur' ? 'selected' : '' }}>Toping Ayam Jamur</option>
                                    <option value="Saus Kecap" {{ old('sub_produk', $cooking->sub_produk) == 'Saus Kecap' ? 'selected' : '' }}>Saus Kecap</option>
                                    <option value="Minyak Bawang" {{ old('sub_produk', $cooking->sub_produk) == 'Minyak Bawang' ? 'selected' : '' }}>Minyak Bawang</option>
                                    <option value="Bawang Putih Sauted" {{ old('sub_produk', $cooking->sub_produk) == 'Bawang Putih Sauted' ? 'selected' : '' }}>Bawang Putih Sauted</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Produk</label>
                                <select id="jenis_produk" name="jenis_produk" class="form-control" required>
                                    <option value="">-- Pilih Jenis Produk --</option>
                                    <option value="RTS" {{ old('jenis_produk', $cooking->jenis_produk) == 'RTS' ? 'selected' : '' }}>RTS (Ready to Serve)</option>
                                    <option value="RTM" {{ old('jenis_produk', $cooking->jenis_produk) == 'RTM' ? 'selected' : '' }}>RTM (Ready to Meal)</option>
                                    <option value="Institusi" {{ old('jenis_produk', $cooking->jenis_produk) == 'Institusi' ? 'selected' : '' }}>Institusi</option>
                                    <option value="Yoshinoya" {{ old('jenis_produk', $cooking->jenis_produk) == 'Yoshinoya' ? 'selected' : '' }}>Yoshinoya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" 
                                value="{{ old('kode_produksi', $cooking->kode_produksi) }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Waktu Proses</label>
                                <div class="input-group">
                                    <input type="time" id="waktu_mulai" name="waktu_mulai" class="form-control" value="{{ old('waktu_mulai', $cooking->waktu_mulai) }}">
                                    <span class="input-group-text">s/d</span>
                                    <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control" value="{{ old('waktu_selesai', $cooking->waktu_selesai) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mesin</label>
                                <select id="nama_mesin"
                                name="nama_mesin[]"
                                class="form-control selectpicker"
                                multiple
                                data-live-search="true"
                                title="-- Pilih Nama Mesin --"
                                data-width="100%" required>
                                @php
                                // decode json nama_mesin jadi array
                                $selectedMesins = is_array($cooking->nama_mesin)
                                ? $cooking->nama_mesin
                                : json_decode($cooking->nama_mesin, true);
                                if (!$selectedMesins) $selectedMesins = [];
                                @endphp

                                <option value="Provisur" {{ in_array('Provisur', $selectedMesins) ? 'selected' : '' }}>Provisur</option>
                                <option value="Kettle Api" {{ in_array('Kettle Api', $selectedMesins) ? 'selected' : '' }}>Kettle Api</option>
                                <option value="Kettle Steam" {{ in_array('Kettle Steam', $selectedMesins) ? 'selected' : '' }}>Kettle Steam</option>
                                <option value="Kettle Api XCG 300" {{ in_array('Kettle Api XCG 300', $selectedMesins) ? 'selected' : '' }}>Kettle Api XCG 300</option>
                                <option value="Alco" {{ in_array('Alco', $selectedMesins) ? 'selected' : '' }}>Alco</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Bagian Pemeriksaan Cooking --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong>  
                        <i class="bi bi-check-circle text-success"></i> Checkbox apabila hasil <u>Oke</u>.  
                        Kosongkan Checkbox apabila hasil <u>Tidak Oke</u>.  
                    </div>

                    <div class="table-responsive">
                        <table id="cookingTable" class="table table-bordered align-middle">
                            <thead class="table-light text-center">
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
                                    <th>Jenis Bahan</th>
                                    <th>Kode Bahan</th>
                                    <th>Jumlah Standar (Kg)</th>
                                    <th>Jumlah Aktual (Kg)</th>
                                    <th>Sensori</th>
                                    <th>Lama Proses<br>(menit)</th>
                                    <th>Mixing Paddle On</th>
                                    <th>Mixing Paddle Off</th>
                                    <th>Pressure (Bar)</th>
                                    <th>Temperature (°C / Api)</th>
                                    <th>Target Temp (°C)</th>
                                    <th>Actual Temp (°C)</th>
                                    <th>Suhu Pusat Produk<br>Setelah 1/30* Menit (°C)</th>
                                    <th>Warna</th>
                                    <th>Aroma</th>
                                    <th>Rasa</th>
                                    <th>Tekstur</th>
                                </tr>
                            </thead>

                            @foreach($pemasakanData as $index => $p)
                            @php
                            $jenis_bahan     = $p['jenis_bahan'] ?? [];
                            $kode_bahan      = $p['kode_bahan'] ?? [];
                            $jumlah_standar  = $p['jumlah_standar'] ?? [];
                            $jumlah_aktual   = $p['jumlah_aktual'] ?? [];
                            $sensori         = $p['sensori'] ?? [];
                            $rowspan         = max(1, count($jenis_bahan));
                            @endphp

                            <tbody class="pemeriksaan">
                                @for($i=0; $i<$rowspan; $i++)
                                <tr>
                                    @if($i==0)
                                    <td rowspan="{{ $rowspan }}">
                                        <input type="time" name="pemasakan[{{ $index }}][pukul]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.pukul', $p['pukul'] ?? '') }}">
                                    </td>
                                    <td rowspan="{{ $rowspan }}">
                                        <input type="text" name="pemasakan[{{ $index }}][tahapan]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.tahapan', $p['tahapan'] ?? '') }}">
                                    </td>
                                    @endif

                                    {{-- Bahan Baku --}}
                                    <td>
                                        <input type="text" name="pemasakan[{{ $index }}][jenis_bahan][]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.jenis_bahan.'.$i, $jenis_bahan[$i] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="text" name="pemasakan[{{ $index }}][kode_bahan][]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.kode_bahan.'.$i, $kode_bahan[$i] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="pemasakan[{{ $index }}][jumlah_standar][]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.jumlah_standar.'.$i, $jumlah_standar[$i] ?? '') }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="pemasakan[{{ $index }}][jumlah_aktual][]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.jumlah_aktual.'.$i, $jumlah_aktual[$i] ?? '') }}">
                                    </td>
                                    {{-- Sensori --}}
                                    <td class="text-center">
                                        <input type="hidden" name="pemasakan[{{ $index }}][sensori][{{ $i }}]" value="Tidak Oke">
                                        <input type="checkbox" class="big-checkbox" 
                                        name="pemasakan[{{ $index }}][sensori][{{ $i }}]" value="Oke"
                                        {{ (!empty($sensori[$i]) && $sensori[$i]=='Oke') ? 'checked' : '' }}>
                                    </td>

                                    @if($i==0)
                                    {{-- Parameter & Produk --}}
                                    <td rowspan="{{ $rowspan }}">
                                        <input type="number" step="0.01" name="pemasakan[{{ $index }}][lama_proses]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.lama_proses', $p['lama_proses'] ?? '') }}">
                                    </td>
                                    {{-- Mixing Paddle On --}}
                                    <td rowspan="{{ $rowspan }}" class="text-center">
                                        <input type="hidden" name="pemasakan[{{ $index }}][paddle_on]" value="0">
                                        <input type="checkbox" class="big-checkbox"
                                        name="pemasakan[{{ $index }}][paddle_on]" value="1"
                                        {{ !empty($p['paddle_on']) ? 'checked' : '' }}>
                                    </td>

                                    {{-- Mixing Paddle Off --}}
                                    <td rowspan="{{ $rowspan }}" class="text-center">
                                        <input type="hidden" name="pemasakan[{{ $index }}][paddle_off]" value="0">
                                        <input type="checkbox" class="big-checkbox"
                                        name="pemasakan[{{ $index }}][paddle_off]" value="1"
                                        {{ !empty($p['paddle_off']) ? 'checked' : '' }}>
                                    </td>
                                    <td rowspan="{{ $rowspan }}">
                                        <input type="number" step="0.01" name="pemasakan[{{ $index }}][pressure]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.pressure', $p['pressure'] ?? '') }}">
                                    </td>
                                    <td rowspan="{{ $rowspan }}">
                                        <input type="text" name="pemasakan[{{ $index }}][temperature]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.temperature', $p['temperature'] ?? '') }}">
                                    </td>
                                    <td rowspan="{{ $rowspan }}">
                                        <input type="number" step="0.01" name="pemasakan[{{ $index }}][target_temp]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.target_temp', $p['target_temp'] ?? '') }}">
                                    </td>
                                    <td rowspan="{{ $rowspan }}">
                                        <input type="number" step="0.01" name="pemasakan[{{ $index }}][actual_temp]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.actual_temp', $p['actual_temp'] ?? '') }}">
                                    </td>
                                    <td rowspan="{{ $rowspan }}">
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" name="pemasakan[{{ $index }}][suhu_pusat]" 
                                            class="form-control"
                                            value="{{ old('pemasakan.'.$index.'.suhu_pusat', $p['suhu_pusat'] ?? '') }}">
                                            <select name="pemasakan[{{ $index }}][suhu_pusat_menit]" class="form-select">
                                                <option value="">Pilih Menit</option>
                                                <option value="1" {{ ($p['suhu_pusat_menit'] ?? '')=='1' ? 'selected' : '' }}>1 Menit</option>
                                                <option value="30" {{ ($p['suhu_pusat_menit'] ?? '')=='30' ? 'selected' : '' }}>30 Menit</option>
                                            </select>
                                        </div>
                                    </td>
                                    {{-- Warna --}}
                                    <td rowspan="{{ $rowspan }}" class="text-center">
                                        <input type="hidden" name="pemasakan[{{ $index }}][warna]" value="Tidak Oke">
                                        <input type="checkbox" class="big-checkbox"
                                        name="pemasakan[{{ $index }}][warna]" value="Oke"
                                        {{ ($p['warna'] ?? '')=='Oke' ? 'checked' : '' }}>
                                    </td>

                                    {{-- Aroma --}}
                                    <td rowspan="{{ $rowspan }}" class="text-center">
                                        <input type="hidden" name="pemasakan[{{ $index }}][aroma]" value="Tidak Oke">
                                        <input type="checkbox" class="big-checkbox"
                                        name="pemasakan[{{ $index }}][aroma]" value="Oke"
                                        {{ ($p['aroma'] ?? '')=='Oke' ? 'checked' : '' }}>
                                    </td>

                                    {{-- Rasa --}}
                                    <td rowspan="{{ $rowspan }}" class="text-center">
                                        <input type="hidden" name="pemasakan[{{ $index }}][rasa]" value="Tidak Oke">
                                        <input type="checkbox" class="big-checkbox"
                                        name="pemasakan[{{ $index }}][rasa]" value="Oke"
                                        {{ ($p['rasa'] ?? '')=='Oke' ? 'checked' : '' }}>
                                    </td>

                                    {{-- Tekstur --}}
                                    <td rowspan="{{ $rowspan }}" class="text-center">
                                        <input type="hidden" name="pemasakan[{{ $index }}][tekstur]" value="Tidak Oke">
                                        <input type="checkbox" class="big-checkbox"
                                        name="pemasakan[{{ $index }}][tekstur]" value="Oke"
                                        {{ ($p['tekstur'] ?? '')=='Oke' ? 'checked' : '' }}>
                                    </td>
                                    <td rowspan="{{ $rowspan }}">
                                        <input type="text" name="pemasakan[{{ $index }}][catatan]" 
                                        class="form-control form-control-sm"
                                        value="{{ old('pemasakan.'.$index.'.catatan', $p['catatan'] ?? '') }}">
                                    </td>
                                    <td rowspan="{{ $rowspan }}" class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus {{ $index==0 ? 'd-none' : '' }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                                @endfor
                            </tbody>
                            @endforeach
                        </table>
                    </div>


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
                    placeholder="Tambahkan catatan bila ada">{{ old('catatan', $cooking->catatan) }}</textarea>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-between mt-3">
              <button type="submit" class="btn btn-success w-auto">
                <i class="bi bi-save"></i> Update
            </button>
            <a href="{{ route('cooking.index') }}" class="btn btn-secondary w-auto">
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
        const table = document.getElementById("cookingTable");

    // Tombol tambah pemeriksaan
        document.getElementById("btnTambahPemeriksaan").addEventListener("click", function () {
            let tbodyList = table.querySelectorAll("tbody.pemeriksaan");
            let lastIndex = tbodyList.length - 1;
            let newIndex = lastIndex + 1;

        // Clone tbody terakhir
            let clone = tbodyList[lastIndex].cloneNode(true);

        // Reset semua input di clone
            clone.querySelectorAll("input, select, textarea").forEach(function (el) {
                if (el.name) {
                // ganti index [0], [1], dst → jadi [newIndex]
                    el.name = el.name.replace(/\[\d+\]/, `[${newIndex}]`);
                }
                if (el.type === "checkbox") {
                el.checked = false; // reset centang
            } else if (el.type === "hidden") {
                // hidden jangan dihapus valuenya, biarkan default
            } else {
                el.value = ""; // kosongkan field input biasa
            }
        });

        // Tampilkan tombol hapus di clone
            let btnHapus = clone.querySelector(".btn-hapus");
            if (btnHapus) {
                btnHapus.classList.remove("d-none");
            }

        // Tambahkan ke tabel
            table.appendChild(clone);
        });

    // Event delegation untuk hapus
        table.addEventListener("click", function (e) {
            if (e.target.closest(".btn-hapus")) {
                let tbodyList = table.querySelectorAll("tbody.pemeriksaan");
                if (tbodyList.length > 1) {
                    let tbody = e.target.closest("tbody.pemeriksaan");
                    tbody.remove();
                } else {
                    alert("Minimal harus ada 1 pemeriksaan!");
                }
            }
        });
    });
</script>

@endsection
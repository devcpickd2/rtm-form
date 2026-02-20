@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Pemasakan Noodle</h4>
            <form method="POST" action="{{ route('noodle.update', $noodle->uuid) }}" enctype="multipart/form-data">
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
                                value="{{ old('date', $noodle->date ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $noodle->shift ?? '') == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $noodle->shift ?? '') == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $noodle->shift ?? '') == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $noodle->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan noodle --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Pemasakan Noodle</strong>
                        <button type="button" id="addnoodleColumn" class="btn btn-primary btn-sm">
                            + Tambah Pemeriksaan
                        </button>
                    </div>
                    <div class="card-body table-responsive" style="overflow-x:auto;">
                        <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>
                            <i class="bi bi-check-circle text-success"></i> Checkbox apabila hasil <u>Oke</u>.
                            Kosongkan Checkbox apabila hasil <u>Tidak Oke</u>.
                        </div>

                        <div class="alert alert-warning mt-2 py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>  
                            <b>USAHAKAN PAKEK TITIK (.) JANGAN PAKAI KOMA(,)</b>.  
                        </div>

                        <table class="table table-bordered table-sm text-center align-middle" id="noodleTable">
                            <thead class="table-light">
                                <tr id="headerRow">
                                    <th style="min-width: 220px; text-align: left;">Parameter</th>
                                    @foreach ($mixing as $index => $data)
                                    <th colspan="5">Pemeriksaan {{ $index + 1 }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Section Noodle --}}
                                <tr>
                                    <td class="text-left">Nama Produk</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="text" name="mixing[{{ $index }}][nama_produk]"
                                        value="{{ $data['nama_produk'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Kode Produksi</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="text" name="mixing[{{ $index }}][kode_produksi]"
                                        value="{{ $data['kode_produksi'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>
                                    @endforeach
                                </tr>

                                {{-- MIXING --}}
                                <tr class="section-header">
                                    <td colspan="{{ 6 * count($mixing) }}" class="text-left fw-bold bg-light">MIXING</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Bahan Utama</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="text" name="mixing[{{ $index }}][bahan_utama]"
                                        value="{{ $data['bahan_utama'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Kode Produksi</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="text" name="mixing[{{ $index }}][kode_bahan]"
                                        value="{{ $data['kode_bahan'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Berat (Kg)</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="number" name="mixing[{{ $index }}][berat_bahan]"
                                        value="{{ $data['berat_bahan'] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endforeach
                                </tr>

                                {{-- Bahan Lain --}}
                                <tr>
                                    <th class="text-left">Bahan Lain</th>
                                    @foreach ($mixing as $index => $data)

                                    <th colspan="3">Kode Produksi</th>
                                    <th colspan="2">Berat (Kg)</th>
                                    @endforeach
                                </tr>

                                @for ($i = 0; $i < 6; $i++)
                                <tr>
                                    @foreach ($mixing as $index => $data)
                                    @if ($index == 0)
                                    {{-- Nama Bahan hanya di pemeriksaan pertama --}}
                                    <td>
                                        <input type="text"
                                        name="mixing[{{ $index }}][bahan_lain][{{ $i }}][nama_bahan]"
                                        value="{{ $data['bahan_lain'][$i]['nama_bahan'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>
                                    @endif

                                    {{-- Kode Produksi --}}
                                    <td colspan="3">
                                        <input type="text"
                                        name="mixing[{{ $index }}][bahan_lain][{{ $i }}][kode_bahan_lain]"
                                        value="{{ $data['bahan_lain'][$i]['kode_bahan_lain'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>

                                    {{-- Berat (Kg) --}}
                                    <td colspan="2">
                                        <input type="number"
                                        name="mixing[{{ $index }}][bahan_lain][{{ $i }}][berat_bahan]"
                                        value="{{ $data['bahan_lain'][$i]['berat_bahan'] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endforeach
                                </tr>
                                @endfor

                                {{-- Waktu Proses, Vacuum, Suhu Adonan --}}
                                @php
                                $multiRows = [
                                'Waktu Proses (Menit)' => 'waktu_proses',
                                'Vacuum (%)' => 'vacuum',
                                'Suhu Adonan (°C)' => 'suhu_adonan',
                                ];
                                @endphp
                                @foreach ($multiRows as $label => $field)
                                <tr>
                                    <td class="text-left">{{ $label }}</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="numner"
                                        name="mixing[{{ $index }}][{{ $field }}][]"
                                        value="{{ $data[$field][$j] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>
                                @endforeach

                                {{-- AGING --}}
                                <tr class="section-header">
                                    <td colspan="{{ 6 * count($mixing) }}" class="text-left fw-bold bg-light">AGING</td>
                                </tr>
                                @php
                                $agingRows = [
                                'Waktu (Menit)' => 'waktu_aging',
                                'RH/Kelembaban (%)' => 'rh_aging',
                                'Suhu Ruangan (°C)' => 'suhu_ruang_aging',
                                ];
                                @endphp
                                @foreach ($agingRows as $label => $field)
                                <tr>
                                    <td class="text-left">{{ $label }}</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="number"
                                        name="mixing[{{ $index }}][{{ $field }}][]"
                                        value="{{ $data[$field][$j] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>
                                @endforeach

                                {{-- ROLLING --}}
                                <tr>
                                    <th class="text-left">ROLLING</th>
                                    @foreach ($mixing as $index => $data)
                                    <th>I</th><th>II</th><th>III</th><th>IV</th><th>V</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Ukuran Tebal (mm)</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="number" name="mixing[{{ $index }}][tebal_rolling][]"
                                        value="{{ $data['tebal_rolling'][$j] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>

                                {{-- CUTTING & SLITTING --}}
                                <tr>
                                    <th class="text-left">CUTTING & SLITTING</th>
                                    @foreach ($mixing as $index => $data)
                                    <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Sampling Berat / 1 cut</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="number" name="mixing[{{ $index }}][sampling_cutting][]"
                                        value="{{ $data['sampling_cutting'][$j] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>

                                {{-- BOILING --}}
                                <tr class="section-header">
                                    <td colspan="{{ 6 * count($mixing) }}" class="text-left fw-bold bg-light">BOILING</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Suhu Setting Water (°C)</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="text" name="mixing[{{ $index }}][suhu_setting_boiling]"
                                        value="{{ $data['suhu_setting_boiling'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Suhu Actual Water (°C)</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="number" name="mixing[{{ $index }}][suhu_actual_boiling][]"
                                        value="{{ $data['suhu_actual_boiling'][$j] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Waktu (menit)</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="number" name="mixing[{{ $index }}][waktu_boiling]"
                                        value="{{ $data['waktu_boiling'] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endforeach
                                </tr>

                                {{-- WASHING --}}
                                <tr class="section-header">
                                    <td colspan="{{ 6 * count($mixing) }}" class="text-left fw-bold bg-light">WASHING</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Suhu Setting Water (°C)</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="text" name="mixing[{{ $index }}][suhu_setting_washing]"
                                        value="{{ $data['suhu_setting_washing'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Suhu Actual Water (°C)</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="number" name="mixing[{{ $index }}][suhu_actual_washing][]"
                                        value="{{ $data['suhu_actual_washing'][$j] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Waktu (menit)</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="number" name="mixing[{{ $index }}][waktu_washing]"
                                        value="{{ $data['waktu_washing'] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endforeach
                                </tr>

                                {{-- COOLING --}}
                                <tr class="section-header">
                                    <td colspan="{{ 6 * count($mixing) }}" class="text-left fw-bold bg-light">COOLING SHOCK</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Suhu Setting Water (°C)</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="text" name="mixing[{{ $index }}][suhu_setting_cooling]"
                                        value="{{ $data['suhu_setting_cooling'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Suhu Actual Water (°C)</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="number" name="mixing[{{ $index }}][suhu_actual_cooling][]"
                                        value="{{ $data['suhu_actual_cooling'][$j] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Waktu (menit)</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="number" name="mixing[{{ $index }}][waktu_cooling]"
                                        value="{{ $data['waktu_cooling'] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endforeach
                                </tr>

                                {{-- LAMA PROSES --}}
                                <tr class="section-header">
                                    <td colspan="{{ 6 * count($mixing) }}" class="text-left fw-bold bg-light">LAMA PROSES</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Jam Mulai</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="time" name="mixing[{{ $index }}][mulai]"
                                        value="{{ $data['mulai'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Jam Selesai</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <input type="time" name="mixing[{{ $index }}][selesai]"
                                        value="{{ $data['selesai'] ?? '' }}"
                                        class="form-control form-control-sm">
                                    </td>
                                    @endforeach
                                </tr>

                                {{-- SENSORI --}}
                                <tr class="section-header">
                                    <td colspan="{{ 6 * count($mixing) }}" class="text-left fw-bold bg-light">SENSORI</td>
                                </tr>
                                <tr>
                                    <td class="text-left">Suhu Produk Akhir (°C)</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="number" name="mixing[{{ $index }}][suhu_akhir][]"
                                        value="{{ $data['suhu_akhir'][$j] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Suhu Produk Setelah 1 Menit (°C)</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="number" name="mixing[{{ $index }}][suhu_after][]"
                                        value="{{ $data['suhu_after'][$j] ?? '' }}"
                                        class="form-control form-control-sm" step="0.01">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Rasa</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="checkbox" name="mixing[{{ $index }}][rasa][]"
                                        value="Oke"
                                        {{ ($data['rasa'][$j] ?? '') === 'Oke' ? 'checked' : '' }}
                                        class="big-checkbox">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Kekenyalan</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="checkbox" name="mixing[{{ $index }}][kekenyalan][]"
                                        value="Oke"
                                        {{ ($data['kekenyalan'][$j] ?? '') === 'Oke' ? 'checked' : '' }}
                                        class="big-checkbox">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-left">Warna</td>
                                    @foreach ($mixing as $index => $data)
                                    @for ($j = 0; $j < 5; $j++)
                                    <td>
                                        <input type="checkbox" name="mixing[{{ $index }}][warna][]"
                                        value="Oke"
                                        {{ ($data['warna'][$j] ?? '') === 'Oke' ? 'checked' : '' }}
                                        class="big-checkbox">
                                    </td>
                                    @endfor
                                    @endforeach
                                </tr>

                                <tr>
                                    <td class="text-left">Aksi</td>
                                    @foreach ($mixing as $index => $data)
                                    <td colspan="5">
                                        <button type="button"
                                        class="btn btn-danger btn-sm removeColumn"
                                        data-index="{{ $index+1 }}">
                                        Hapus
                                    </button>
                                </td>
                                @endforeach
                            </tr>


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
                    placeholder="Tambahkan catatan bila ada">{{ old('catatan', $noodle->catatan ?? '') }}</textarea>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-success w-auto">
                    <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('noodle.index') }}" class="btn btn-secondary w-auto">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
</div>

<!-- jQuery dan bootstrap-select tetap sama -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

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

{{-- Style dan script cloning column sama dengan versi input --}}
<style>
    #noodleTable th { background-color: #f8f9fa; font-weight: bold; text-align: center; }
    #noodleTable td { padding: 10px; vertical-align: middle; text-align: center; }
    #noodleTable tbody tr:nth-child(odd) { background-color: #f9f9f9; }
    #noodleTable tbody tr:hover { background-color: #e9f7fe; }
    .form-control-sm { min-width: 120px; }
    .big-checkbox { width: 24px; height: 24px; transform: scale(1.4); cursor: pointer; }
    .section-header td { background: #f1f3f4 !important; font-weight: bold; }
</style>

<!-- Ganti juga skrip lama dengan skrip ini -->
<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });

    document.addEventListener("DOMContentLoaded", function () {
        const selectProduk = document.getElementById("nama_produk");
        const addColumnBtn = document.getElementById("addnoodleColumn");
        const noodleTable = document.getElementById("noodleTable");
        const headerRow = document.getElementById("headerRow");
    const colPerPemeriksaan = 5; // jumlah kolom per pemeriksaan (I..V)

    // -----------------------
    // Fungsi sinkron nama produk
    // -----------------------
    function syncNamaProduk() {
        const allNamaProdukInputs = document.querySelectorAll("input[name^='mixing'][name$='[nama_produk]']");
        allNamaProdukInputs.forEach(input => {
            input.value = selectProduk.value;
        });
    }

    // Sinkron saat dropdown berubah
    selectProduk.addEventListener("change", syncNamaProduk);

    // Sinkron saat halaman load
    if (selectProduk.value) {
        syncNamaProduk();
    }

    // -----------------------
    // Fungsi cloning dan manipulasi tabel
    // -----------------------
    const getPemeriksaanCount = () => headerRow.children.length - 1; 

    function clearInputs(container) {
        container.querySelectorAll('input').forEach(inp => {
            if (inp.type === 'checkbox') inp.checked = false;
            else inp.value = '';
        });
    }

    function setIndexFor(container, zeroIndex) {
        container.querySelectorAll('input').forEach(inp => {
            if (!inp.name) return;
            inp.name = inp.name.replace(/(mixing\[)\d+/, `$1${zeroIndex}`);
        });
    }

    function updateSectionHeaderColspan() {
        const total = getPemeriksaanCount();
        noodleTable.querySelectorAll("tbody tr.section-header td").forEach(td => {
            td.colSpan = 1 + total * colPerPemeriksaan;
        });
    }

    function addHeaderCell(newIndex) {
        const th = document.createElement("th");
        th.colSpan = colPerPemeriksaan;
        th.textContent = `Pemeriksaan ${newIndex}`;
        headerRow.appendChild(th);
    }

    function addAksiCell(newIndex) {
        const aksiRow = Array.from(noodleTable.tBodies[0].rows).find(r =>
            (r.children[0].textContent || '').trim() === 'Aksi'
            );
        const td = document.createElement("td");
        td.colSpan = colPerPemeriksaan;
        td.innerHTML = `<button type="button" class="btn btn-danger btn-sm removeColumn" data-index="${newIndex}">Hapus</button>`;
        aksiRow.appendChild(td);
    }

    function addPemeriksaan() {
        const existing = getPemeriksaanCount();
        const newIndex = existing + 1;
        addHeaderCell(newIndex);

        noodleTable.querySelectorAll("tbody tr").forEach((row) => {
            if (row.classList.contains("section-header")) return;

            const firstCellText = (row.children[0].textContent || '').trim();
            if (firstCellText === "Aksi") { addAksiCell(newIndex); return; }
            if (row.classList.contains("no-clone")) return;

            const children = Array.from(row.children);
            const cellsPerBlock = Math.round((children.length - 1) / existing) || 1;
            const templateCells = children.slice(-cellsPerBlock);

            templateCells.forEach(td => {
                const newTd = td.cloneNode(true);
                clearInputs(newTd);
                setIndexFor(newTd, newIndex - 1);
                row.appendChild(newTd);
            });
        });

        updateSectionHeaderColspan();
        syncNamaProduk(); // pastikan kolom baru ikut nama produk
    }

    function renumberAll() {
        const total = getPemeriksaanCount();
        const aksiRow = Array.from(noodleTable.tBodies[0].rows).find(r =>
            (r.children[0].textContent || '').trim() === 'Aksi'
            );
        if (aksiRow) {
            let idx = 1;
            aksiRow.querySelectorAll('button.removeColumn').forEach(btn => {
                btn.dataset.index = idx++;
            });
        }

        noodleTable.querySelectorAll("tbody tr").forEach((row) => {
            if (row.classList.contains("section-header")) return;
            const firstCellText = (row.children[0].textContent || '').trim();
            if (firstCellText === "Aksi" || row.classList.contains("no-clone")) return;

            const children = Array.from(row.children);
            const cellsPerBlock = Math.round((children.length - 1) / total) || 1;

            for (let b = 1; b <= total; b++) {
                const start = 1 + (b - 1) * cellsPerBlock;
                const blockCells = children.slice(start, start + cellsPerBlock);
                blockCells.forEach(td => setIndexFor(td, b - 1));
            }
        });

        updateSectionHeaderColspan();
        syncNamaProduk(); // pastikan semua kolom tetap sinkron
    }

    function removePemeriksaan(index1Based) {
        const totalBefore = getPemeriksaanCount();
        if (index1Based < 1 || index1Based > totalBefore) return;

        const thToRemove = headerRow.children[index1Based];
        if (thToRemove) headerRow.removeChild(thToRemove);

        noodleTable.querySelectorAll("tbody tr").forEach((row) => {
            if (row.classList.contains("section-header")) return;
            const firstCellText = (row.children[0].textContent || '').trim();

            if (firstCellText === "Aksi") {
                const btn = row.querySelector(`button.removeColumn[data-index='${index1Based}']`);
                if (btn && btn.parentElement) btn.parentElement.remove();
                return;
            }
            if (row.classList.contains("no-clone")) return;

            const children = Array.from(row.children);
            const cellsPerBlock = Math.round((children.length - 1) / totalBefore) || 1;
            const start = 1 + (index1Based - 1) * cellsPerBlock;

            for (let i = 0; i < cellsPerBlock; i++) {
                const target = row.children[start];
                if (target) row.removeChild(target);
            }
        });

        renumberAll();
    }

    updateSectionHeaderColspan();

    addColumnBtn.addEventListener("click", function() {
        addPemeriksaan();
        renumberAll(); 
    });

    noodleTable.addEventListener("click", function (e) {
        const btn = e.target.closest('button.removeColumn');
        if (!btn) return;
        const idx = parseInt(btn.dataset.index, 10);
        if (Number.isNaN(idx)) return;
        removePemeriksaan(idx);
        renumberAll();
    });
});
</script>


@endsection

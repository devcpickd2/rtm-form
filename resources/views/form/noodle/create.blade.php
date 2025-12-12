@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Pemasakan Noodle</h4>
            <form method="POST" action="{{ route('noodle.store') }}" enctype="multipart/form-data">
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
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker"
                                data-live-search="true" title="Ketik nama produk..." required>
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

            {{-- Bagian Pemeriksaan noodle --}}
            <div class="card mb-4">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <strong>Pemasakan Noodle</strong>
                    <button type="button" id="addnoodleColumn" class="btn btn-primary btn-sm">
                        + Tambah Pemeriksaan
                    </button>
                </div>
                <div class="card-body table-responsive" style="overflow-x:auto;">
                    {{-- Note Petunjuk Checkbox --}}
                    <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong>
                        <i class="bi bi-check-circle text-success"></i> Checkbox apabila hasil <u>Oke</u>.
                        Kosongkan Checkbox apabila hasil <u>Tidak Oke</u>.
                    </div>

                    <table class="table table-bordered table-sm text-center align-middle" id="noodleTable">
                        <thead class="table-light">
                            <tr id="headerRow">
                                <th style="min-width: 220px; text-align: left;">Parameter</th>
                                <th colspan="5">Pemeriksaan 1</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Section Noodle --}}
                            <tr>
                                <td class="text-left">Nama Produk</td>
                                <td colspan="5">
                                    <input type="text" name="mixing[0][nama_produk]" class="form-control form-control-sm">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-left">Kode Produksi</td>
                                <td colspan="5">
                                    <input type="text" name="mixing[0][kode_produksi]" class="form-control form-control-sm">
                                </td>
                            </tr>

                            <tr class="section-header">
                                <td colspan="6" class="text-left fw-bold bg-light">MIXING</td>
                            </tr>

                            <tr>
                                <td class="text-left">Bahan Utama</td>
                                <td colspan="5">
                                    <input type="text" name="mixing[0][bahan_utama]" class="form-control form-control-sm">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-left">Kode Produksi</td>
                                <td colspan="5">
                                    <input type="text" name="mixing[0][kode_bahan]" class="form-control form-control-sm">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-left">Berat (Kg)</td>
                                <td colspan="5">
                                    <input type="text" name="mixing[0][berat_bahan]" class="form-control form-control-sm">
                                </td>
                            </tr>

                            
                            {{-- Bagian Bahan Lain -> setiap pemeriksaan punya 2 TD: (colspan=3) + (colspan=2) --}}
                            <tr>
                                <th class="text-left">Bahan Lain</th>
                                <th colspan="3">Kode Produksi</th>
                                <th colspan="2">Berat (Kg)</th>
                            </tr>

                            @for ($i = 0; $i < 6; $i++)
                            <tr>
                                <td>
                                    <input type="text"
                                    name="mixing[0][bahan_lain][{{ $i }}][nama_bahan]"
                                    class="form-control form-control-sm">
                                </td>

                                {{-- Kode Produksi (colspan=3) --}}
                                <td colspan="3">
                                    <input type="text"
                                    name="mixing[0][bahan_lain][{{ $i }}][kode_bahan_lain]"
                                    class="form-control form-control-sm">
                                </td>

                                {{-- Berat (colspan=2) --}}
                                <td colspan="2">
                                    <input type="text"
                                    name="mixing[0][bahan_lain][{{ $i }}][berat_bahan]"
                                    class="form-control form-control-sm">
                                </td>
                            </tr>
                            @endfor

                            <tr>
                                <td class="text-left">Waktu Proses (Menit)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][waktu_proses][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr>
                                <td class="text-left">Vacuum (%)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][vacuum][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr>
                                <td class="text-left">Suhu Adonan (°C)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][suhu_adonan][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr class="section-header">
                                <td colspan="6" class="text-left fw-bold bg-light">AGING</td>
                            </tr>

                            <tr>
                                <td class="text-left">Waktu (Menit)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][waktu_aging][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr>
                                <td class="text-left">RH/Kelembaban (%)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][rh_aging][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr>
                                <td class="text-left">Suhu Ruangan (°C)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][suhu_ruang_aging][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr>
                                <th class="text-left">ROLLING</th>
                                <th>I</th><th>II</th><th>III</th><th>IV</th><th>V</th>
                            </tr>

                            <tr>
                                <td class="text-left">Ukuran Tebal (mm)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][tebal_rolling][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr>
                                <th class="text-left">CUTTING & SLITTING</th>
                                <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th>
                            </tr>

                            <tr>
                                <td class="text-left">Sampling Berat / 1 cut</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][sampling_cutting][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr class="section-header">
                                <td colspan="6" class="text-left fw-bold bg-light">BOILING</td>
                            </tr>

                            <tr>
                                <td class="text-left">Suhu Setting Water (°C)</td>
                                <td colspan="5"><input type="text" name="mixing[0][suhu_setting_boiling]" class="form-control form-control-sm"></td>
                            </tr>

                            <tr>
                                <td class="text-left">Suhu Actual Water (°C)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][suhu_actual_boiling][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr>
                                <td class="text-left">Waktu (menit)</td>
                                <td colspan="5"><input type="number" name="mixing[0][waktu_boiling]" class="form-control form-control-sm" step="0.01"></td>
                            </tr>

                            <tr class="section-header">
                                <td colspan="6" class="text-left fw-bold bg-light">WASHING</td>
                            </tr>

                            <tr>
                                <td class="text-left">Suhu Setting Water (°C)</td>
                                <td colspan="5"><input type="text" name="mixing[0][suhu_setting_washing]" class="form-control form-control-sm"></td>
                            </tr>

                            <tr>
                                <td class="text-left">Suhu Actual Water (°C)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][suhu_actual_washing][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr>
                                <td class="text-left">Waktu (menit)</td>
                                <td colspan="5"><input type="number" name="mixing[0][waktu_washing]" class="form-control form-control-sm"></td>
                            </tr>

                            <tr class="section-header">
                                <td colspan="6" class="text-left fw-bold bg-light">COOLING SHOCK</td>
                            </tr>

                            <tr>
                                <td class="text-left">Suhu Setting Water (°C)</td>
                                <td colspan="5"><input type="text" name="mixing[0][suhu_setting_cooling]" class="form-control form-control-sm"></td>
                            </tr>

                            <tr>
                                <td class="text-left">Suhu Actual Water (°C)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][suhu_actual_cooling][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>

                            <tr>
                                <td class="text-left">Waktu (menit)</td>
                                <td colspan="5"><input type="number" name="mixing[0][waktu_cooling]" class="form-control form-control-sm"></td>
                            </tr>

                            {{-- Section LAMA PROSES --}}
                            <tr class="section-header">
                                <td colspan="6" class="text-left fw-bold bg-light">LAMA PROSES</td>
                            </tr>

                            <tr>
                                <td class="text-left">Jam Mulai</td>
                                <td colspan="5"><input type="time" name="mixing[0][mulai]" class="form-control form-control-sm"></td>
                            </tr>
                            <tr>
                                <td class="text-left">Jam Selesai</td>
                                <td colspan="5"><input type="time" name="mixing[0][selesai]" class="form-control form-control-sm"></td>
                            </tr>

                            {{-- Section SENSORI --}}
                            <tr class="section-header">
                                <td colspan="6" class="text-left fw-bold bg-light">SENSORI</td>
                            </tr>

                            <tr>
                                <td class="text-left">Suhu Produk Akhir (°C)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][suhu_akhir][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>
                            <tr>
                                <td class="text-left">Suhu Produk Setelah 1 Menit (°C)</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="text" name="mixing[0][suhu_actual_cooling][]" class="form-control form-control-sm"></td>
                                @endfor
                            </tr>
                            <tr>
                                <td class="text-left">Rasa</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="checkbox" name="mixing[0][rasa][]" value="Oke" class="big-checkbox"></td>
                                @endfor
                            </tr>
                            <tr>
                                <td class="text-left">Kekenyalan</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="checkbox" name="mixing[0][kekenyalan][]" value="Oke" class="big-checkbox"></td>
                                @endfor
                            </tr>
                            <tr>
                                <td class="text-left">Warna</td>
                                @for ($i = 0; $i < 5; $i++)
                                <td><input type="checkbox" name="mixing[0][warna][]" value="Oke" class="big-checkbox"></td>
                                @endfor
                            </tr>

                            {{-- Section Aksi --}}
                            <tr>
                                <td class="text-left">Aksi</td>
                                {{-- tombol untuk Pemeriksaan 1 --}}
                                <td colspan="5">
                                    <button type="button" class="btn btn-danger btn-sm removeColumn" data-index="1">Hapus</button>
                                </td>
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
                    placeholder="Tambahkan catatan bila ada">{{ old('catatan', $data->catatan ?? '') }}</textarea>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-success w-auto">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('noodle.index') }}" class="btn btn-secondary w-auto">
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
<style>
    #noodleTable th { background-color: #f8f9fa; font-weight: bold; text-align: center; }
    #noodleTable td { padding: 10px; vertical-align: middle; text-align: center; }
    #noodleTable tbody tr:nth-child(odd) { background-color: #f9f9f9; }
    #noodleTable tbody tr:hover { background-color: #e9f7fe; }
    .form-control-sm { min-width: 120px; }
    .big-checkbox { width: 24px; height: 24px; transform: scale(1.4); cursor: pointer; }
    .section-header td { background: #f1f3f4 !important; font-weight: bold; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectProduk = document.getElementById("nama_produk");
        const inputNamaProduk = document.querySelector("input[name='mixing[0][nama_produk]']");

        selectProduk.addEventListener("change", function () {
        inputNamaProduk.value = this.value; // sinkron otomatis
    });

    // inisialisasi langsung jika ada value awal
        if (selectProduk.value) {
            inputNamaProduk.value = selectProduk.value;
        }
    });
</script>

<!-- Ganti juga skrip lama dengan skrip ini -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectProduk = document.getElementById("nama_produk");
        const noodleTable = document.getElementById("noodleTable");
        const headerRow = document.getElementById("headerRow");
        const addColumnBtn = document.getElementById("addnoodleColumn");
        const colPerPemeriksaan = 5;

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
                inp.name = inp.name.replace(/(mixing\[)\d+(\])/, `$1${zeroIndex}$2`);
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

    // --- Fungsi tambahan untuk sync nama_produk ---
        function syncNamaProdukToColumn(indexZeroBased) {
            const input = noodleTable.querySelector(`input[name='mixing[${indexZeroBased}][nama_produk]']`);
            if (input) input.value = selectProduk.value;
        }

        function syncAllNamaProduk() {
            const totalColumns = getPemeriksaanCount();
            for (let i = 0; i < totalColumns; i++) {
                syncNamaProdukToColumn(i);
            }
        }

        selectProduk.addEventListener("change", syncAllNamaProduk);

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

                // jika baris nama_produk, langsung sinkron
                    if (firstCellText === "Nama Produk") {
                        syncNamaProdukToColumn(newIndex - 1);
                    }
                });
            });

            updateSectionHeaderColspan();
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
        syncAllNamaProduk(); // pastikan semua kolom nama_produk sinkron
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
    syncAllNamaProduk();

    addColumnBtn.addEventListener("click", addPemeriksaan);
    noodleTable.addEventListener("click", function (e) {
        const btn = e.target.closest('button.removeColumn');
        if (!btn) return;
        const idx = parseInt(btn.dataset.index, 10);
        if (Number.isNaN(idx)) return;
        removePemeriksaan(idx);
    });
});

</script>
@endsection

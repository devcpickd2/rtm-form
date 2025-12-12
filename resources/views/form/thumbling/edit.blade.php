@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Proses Thumbling</h4>

            <form method="POST" action="{{ route('thumbling.update', $thumbling->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- ===========================
                IDENTITAS PEMERIKSAAN
                =========================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control"
                                value="{{ old('date', $thumbling->date) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $thumbling->shift)=='1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $thumbling->shift)=='2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $thumbling->shift)=='3' ? 'selected' : '' }}>Shift 3</option>
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
                                        {{ old('nama_produk', $thumbling->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===========================
                PROSES THUMBLING (1 BATCH FIX)
                =========================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <strong>Proses Thumbling</strong>
                    </div>

                    <div class="card-body table-responsive">

                        <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: .9rem;">
                            <strong>Kondisi daging:</strong> Aroma segar, tidak busuk, bebas kontaminasi.
                        </div>

                        <table class="table table-bordered table-sm text-center align-middle" id="thumblingTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 200px; text-align:left;">Parameter</th>
                                    <th colspan="4">Input</th>
                                </tr>
                            </thead>

                            <tbody>

                                {{-- ======================
                                IDENTITAS DAGING
                                ======================= --}}
                                <tr>
                                    <td class="text-left"><strong>BATCH NO.</strong></td>
                                    <td colspan="4">
                                        <input type="text" name="kode_produksi" class="form-control form-control-sm" 
                                        value="{{ old('kode_produksi', $thumbling->kode_produksi) }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-left"><strong>IDENTIFIKASI DAGING</strong></td>
                                    <td colspan="4">
                                        <input type="text" name="identifikasi_daging" class="form-control form-control-sm" 
                                        value="{{ old('identifikasi_daging', $thumbling->identifikasi_daging) }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-left">Asal</td>
                                    <td colspan="4">
                                        <input type="text" name="asal_daging" class="form-control form-control-sm" 
                                        value="{{ old('asal_daging', $thumbling->asal_daging) }}">
                                    </td>
                                </tr>

                                {{-- ======================
                                KODE DAGING
                                ======================= --}}
                                <tr>
                                    <td class="text-left">Tanggal Produksi / Kode</td>
                                    @for($i=0;$i<4;$i++)
                                    <td>
                                        <input type="text" name="kode_daging[]" class="form-control form-control-sm"
                                        value="{{ old('kode_daging.'.$i, $thumbling->kode_daging[$i] ?? '') }}">
                                    </td>
                                    @endfor
                                </tr>

                                <tr>
                                    <td class="text-left">Berat (kg)</td>
                                    @for($i=0;$i<4;$i++)
                                    <td>
                                        <input type="number" step="0.01" name="berat_daging[]" class="form-control form-control-sm"
                                        value="{{ old('berat_daging.'.$i, $thumbling->berat_daging[$i] ?? '') }}">
                                    </td>
                                    @endfor
                                </tr>

                                <tr>
                                    <td class="text-left">Suhu Daging (0–10°C)</td>
                                    @for($i = 0; $i < 4; $i++)
                                    <td>
                                        @for($s = 0; $s < 4; $s++)
                                        <input type="number" step="0.1" 
                                        name="suhu_daging[{{ $i }}][]" 
                                        class="form-control form-control-sm mb-1 suhu-daging"
                                        value="{{ old('suhu_daging.'.$i.'.'.$s, $thumbling->suhu_daging[$i][$s] ?? '') }}">
                                        @endfor
                                    </td>
                                    @endfor
                                </tr>

                                <tr class="rata-row">
                                    <td class="text-left">Rata-rata</td>
                                    @for($i=0;$i<4;$i++)
                                    <td>
                                        <input type="number" step="0.01" name="rata_daging[]" class="form-control form-control-sm"
                                        value="{{ old('rata_daging.'.$i, $thumbling->rata_daging[$i] ?? '') }}">
                                    </td>
                                    @endfor
                                </tr>

                                <tr>
                                    <td class="text-left">Kondisi Daging</td>
                                    <td colspan="4">
                                        <input type="text" name="kondisi_daging" class="form-control form-control-sm"
                                        value="{{ old('kondisi_daging', $thumbling->kondisi_daging) }}">
                                    </td>
                                </tr>

                                {{-- ======================
                                MARINADE
                                ======================= --}}
                                <tr><td class="text-left"><strong>MARINADE</strong></td><td colspan="4"></td></tr>

                                <tr>
                                    <td class="text-left">Bahan Utama</td>
                                    @for($i=0;$i<4;$i++)
                                    <td>
                                        <input type="text" name="premix[]" class="form-control form-control-sm"
                                        value="{{ old('premix.'.$i, $thumbling->premix[$i] ?? '') }}">
                                    </td>
                                    @endfor
                                </tr>

                                <tr>
                                    <td class="text-left">Kode</td>
                                    @for($i=0;$i<4;$i++)
                                    <td>
                                        <input type="text" name="kode_premix[]" class="form-control form-control-sm"
                                        value="{{ old('kode_premix.'.$i, $thumbling->kode_premix[$i] ?? '') }}">
                                    </td>
                                    @endfor
                                </tr>

                                <tr>
                                    <td class="text-left">Berat (kg)</td>
                                    @for($i=0;$i<4;$i++)
                                    <td>
                                        <input type="number" step="0.01" name="berat_premix[]" class="form-control form-control-sm"
                                        value="{{ old('berat_premix.'.$i, $thumbling->berat_premix[$i] ?? '') }}">
                                    </td>
                                    @endfor
                                </tr>

                                {{-- ======================
                                BAHAN LAIN (DINAMIS)
                                ======================= --}}
                                <tr class="text-center">
                                    <th class="text-left">Bahan Lain</th>
                                    <th colspan="2">Kode</th>
                                    <th>Berat (kg)</th>
                                    <th>Sensori</th>
                                </tr>
                            </tbody>
                            <tbody id="bahanLainWrapper">
                                @if($thumbling->bahan_lain)
                                    @foreach($thumbling->bahan_lain as $index => $bahan)
                                    <tr>
                                        <td><input type="text" name="bahan_lain[{{ $index }}][premix]" class="form-control form-control-sm" value="{{ $bahan['premix'] ?? '' }}"></td>
                                        <td colspan="2"><input type="text" name="bahan_lain[{{ $index }}][kode]" class="form-control form-control-sm" value="{{ $bahan['kode'] ?? '' }}"></td>
                                        <td><input type="number" step="0.01" name="bahan_lain[{{ $index }}][berat]" class="form-control form-control-sm" value="{{ $bahan['berat'] ?? '' }}"></td>
                                        <td class="d-flex gap-1">
                                            <select name="bahan_lain[{{ $index }}][sens]" class="form-control form-select-sm">
                                                <option value=""> -- Pilih -- </option>
                                                <option value="sesuai" {{ ($bahan['sens'] ?? '') == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                                <option value="tidak_sesuai" {{ ($bahan['sens'] ?? '') == 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                            </select>
                                            <button type="button" class="btn btn-danger btn-sm removeBahan">X</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td><input type="text" name="bahan_lain[0][premix]" class="form-control form-control-sm"></td>
                                        <td colspan="2"><input type="text" name="bahan_lain[0][kode]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="0.01" name="bahan_lain[0][berat]" class="form-control form-control-sm"></td>
                                        <td class="d-flex gap-1">
                                            <select name="bahan_lain[0][sens]" class="form-control form-select-sm">
                                                <option value=""> -- Pilih -- </option>
                                                <option value="sesuai">Sesuai</option>
                                                <option value="tidak_sesuai">Tidak Sesuai</option>
                                            </select>
                                            <button type="button" class="btn btn-danger btn-sm removeBahan">X</button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-right">
                                        <button type="button" class="btn btn-sm btn-primary" id="addBahanLain">
                                            + Tambah Baris Bahan Lain
                                        </button>
                                    </td>
                                </tr>

                                {{-- ======================
                                PARAMETER CAIRAN
                                ======================= --}}
                                <tr><td class="text-left">Air (kg)</td><td colspan="4"><input type="number" step="0.01" name="air" class="form-control" value="{{ old('air', $thumbling->air) }}"></td></tr>
                                <tr><td class="text-left">Suhu Air</td><td colspan="4"><input type="number" step="0.1" name="suhu_air" class="form-control" value="{{ old('suhu_air', $thumbling->suhu_air) }}"></td></tr>
                                <tr><td class="text-left">Suhu Marinade</td><td colspan="4"><input type="number" step="0.1" name="suhu_marinade" class="form-control" value="{{ old('suhu_marinade', $thumbling->suhu_marinade) }}"></td></tr>
                                <tr><td class="text-left">Lama Pengadukan</td><td colspan="4"><input type="number" name="lama_pengadukan" class="form-control" value="{{ old('lama_pengadukan', $thumbling->lama_pengadukan) }}"></td></tr>
                                <tr><td class="text-left">Marinade Brix – Salinity</td><td colspan="4"><input type="text" name="marinade_brix_salinity" class="form-control" value="{{ old('marinade_brix_salinity', $thumbling->marinade_brix_salinity) }}"></td></tr>

                                {{-- ======================
                                PARAMETER THUMBLING
                                ======================= --}}
                                <tr><td class="text-left align-middle"><strong>PARAMETER THUMBLING</strong></td><td colspan="4"></td></tr>

                                <tr><td class="text-left align-middle">Drum On (Menit)</td><td colspan="4"><input type="number" name="drum_on" class="form-control" value="{{ old('drum_on', $thumbling->drum_on) }}"></td></tr>
                                <tr><td class="text-left align-middle">Drum Off (Menit)</td><td colspan="4"><input type="number" name="drum_off" class="form-control" value="{{ old('drum_off', $thumbling->drum_off) }}"></td></tr>
                                <tr><td class="text-left align-middle">Drum Speed (RPM)</td><td colspan="4"><input type="number" name="drum_speed" class="form-control" value="{{ old('drum_speed', $thumbling->drum_speed) }}"></td></tr>
                                <tr><td class="text-left align-middle">Vacuum Time</td><td colspan="4"><input type="text" name="vacuum_time" class="form-control" value="{{ old('vacuum_time', $thumbling->vacuum_time) }}"></td></tr>
                                <tr><td class="text-left align-middle">Total Time</td><td colspan="4"><input type="text" name="total_time" class="form-control" value="{{ old('total_time', $thumbling->total_time) }}"></td></tr>

                                <tr>
                                    <td class="text-left align-middle">Mulai – Selesai</td>
                                    <td colspan="2"><input type="time" name="waktu_mulai" class="form-control" value="{{ old('waktu_mulai', $thumbling->waktu_mulai) }}"></td>
                                    <td colspan="2"><input type="time" name="waktu_selesai" class="form-control" value="{{ old('waktu_selesai', $thumbling->waktu_selesai) }}"></td>
                                </tr>

                                {{-- ======================
                                HASIL THUMBLING
                                ======================= --}}
                                <tr><td class="text-left align-middle"><strong>HASIL THUMBLING</strong></td><td colspan="4"></td></tr>

                                <tr>
                                    <td rowspan="2" class="text-left align-middle">Suhu Daging</td>

                                    {{-- Baris 1: 4 kolom --}}
                                    @for($i = 0; $i < 4; $i++)
                                    <td>
                                        <input type="number" step="0.1"
                                        name="suhu_daging_thumbling[{{ $i }}]"
                                        class="form-control form-control-sm suhu-hasil"
                                        value="{{ old('suhu_daging_thumbling.'.$i, $thumbling->suhu_daging_thumbling[$i] ?? '') }}">
                                    </td>
                                    @endfor
                                </tr>

                                <tr>
                                    {{-- Baris 2: 4 kolom --}}
                                    @for($i = 4; $i < 8; $i++)
                                    <td>
                                        <input type="number" step="0.1"
                                        name="suhu_daging_thumbling[{{ $i }}]"
                                        class="form-control form-control-sm suhu-hasil"
                                        value="{{ old('suhu_daging_thumbling.'.$i, $thumbling->suhu_daging_thumbling[$i] ?? '') }}">
                                    </td>
                                    @endfor
                                </tr>

                                <tr class="hasil-rata-row">
                                    <td class="text-left">Rata-rata</td>
                                    <td colspan="4">
                                        <input type="number" step="0.01"
                                        name="rata_daging_thumbling"
                                        class="form-control rata-rata-hasil"
                                        value="{{ old('rata_daging_thumbling', $thumbling->rata_daging_thumbling) }}">
                                    </td>
                                </tr>

                                {{-- ======================
                                KONDISI & CATATAN
                                ======================= --}}
                                <tr>
                                    <td class="text-left"><strong>Kondisi</strong></td>
                                    <td colspan="4">
                                        <input type="text" name="kondisi_daging_akhir"
                                        class="form-control"
                                        value="{{ old('kondisi_daging_akhir', $thumbling->kondisi_daging_akhir) }}">
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-left"><strong>Catatan</strong></td>
                                    <td colspan="4">
                                        <textarea name="catatan_akhir" class="form-control" rows="2">{{ old('catatan_akhir', $thumbling->catatan_akhir) }}</textarea>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- CATATAN TAMBAHAN --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $thumbling->catatan) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-success"><i class="bi bi-save"></i> Update</button>
                    <a href="{{ route('thumbling.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });

    let bahanIndex = {{ count($thumbling->bahan_lain ?? [0]) }}; // index berikutnya

    $("#addBahanLain").click(function() {
        let row = `
            <tr>
                <td><input type="text" name="bahan_lain[${bahanIndex}][premix]" class="form-control form-control-sm"></td>
                <td colspan="2"><input type="text" name="bahan_lain[${bahanIndex}][kode]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="bahan_lain[${bahanIndex}][berat]" class="form-control form-control-sm"></td>
                <td class="d-flex gap-1">
                    <select name="bahan_lain[${bahanIndex}][sens]" class="form-control form-select-sm">
                        <option value=""> -- Pilih -- </option>
                        <option value="sesuai">Sesuai</option>
                        <option value="tidak_sesuai">Tidak Sesuai</option>
                    </select>
                    <button type="button" class="btn btn-danger btn-sm removeBahan">X</button>
                </td>
            </tr>
        `;
        $("#bahanLainWrapper").append(row);
        bahanIndex++;
    });

    $(document).on('click', '.removeBahan', function() {
        $(this).closest('tr').remove();
    });

    // Set default tanggal & shift
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");

        if(!dateInput.value){
            let now = new Date();
            let yyyy = now.getFullYear();
            let mm = String(now.getMonth() + 1).padStart(2, '0');
            let dd = String(now.getDate()).padStart(2, '0');
            dateInput.value = `${yyyy}-${mm}-${dd}`;
        }

        if(!shiftInput.value){
            let hh = new Date().getHours();
            if (hh >= 7 && hh < 15) shiftInput.value = "1";
            else if (hh >= 15 && hh < 23) shiftInput.value = "2";
            else shiftInput.value = "3";
        }
    });

    // Hitung rata-rata suhu
    document.addEventListener("input", function (e) {
        if (e.target.classList.contains("suhu-daging")) {
            const td = e.target.closest("td");
            const row = td.parentElement.parentElement.querySelector(".rata-row");
            if (!row) return;
            const col = [...td.parentElement.children].indexOf(td);
            const target = row.children[col].querySelector("input");
            const vals = [...td.querySelectorAll("input")].map(x => parseFloat(x.value)).filter(v => !isNaN(v));
            target.value = vals.length ? (vals.reduce((a,b)=>a+b,0)/vals.length).toFixed(2) : "";
        }

        if (e.target.classList.contains("suhu-hasil")) {
            const vals = [...document.querySelectorAll(".suhu-hasil")].map(x => parseFloat(x.value)).filter(v => !isNaN(v));
            document.querySelector(".rata-rata-hasil").value = vals.length ? (vals.reduce((a,b)=>a+b,0)/vals.length).toFixed(2) : "";
        }
    });
</script>

@endsection

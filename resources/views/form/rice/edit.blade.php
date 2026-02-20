@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Pemasakan dengan Rice Cooker</h4>
            <form method="POST" action="{{ route('rice.update', $data->uuid) }}" enctype="multipart/form-data">
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
                                value="{{ old('date', $data->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $data->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $data->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $data->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $data->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan Rice Cooker --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Pemasakan dengan Rice Cooker</strong>
                        <button type="button" id="addriceColumn" class="btn btn-primary btn-sm">
                            + Tambah Pemeriksaan
                        </button>
                    </div>
                    <div class="card-body table-responsive" style="overflow-x:auto;">
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
                                @foreach($cookerData as $i => $cooker)
                                <th>Pemeriksaan {{ $i+1 }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Identitas Rice Cooker --}}
                            <tr data-field="kode_beras"><td class="text-left">Kode Beras</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="text" name="cooker[{{ $i }}][kode_beras]" value="{{ $cooker['kode_beras'] ?? '' }}" class="form-control form-control-sm"></td>
                                @endforeach
                            </tr>
                            <tr data-field="berat"><td class="text-left">Berat (kg)</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="number" name="cooker[{{ $i }}][berat]" value="{{ $cooker['berat'] ?? '' }}" class="form-control form-control-sm" step="0.01"></td>
                                @endforeach
                            </tr>
                            <tr data-field="kode_produksi"><td class="text-left">Kode Produksi</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="text" name="cooker[{{ $i }}][kode_produksi]" value="{{ $cooker['kode_produksi'] ?? '' }}" class="form-control form-control-sm"></td>
                                @endforeach
                            </tr>
                            <tr data-field="basket"><td class="text-left">Basket No.</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="number" name="cooker[{{ $i }}][basket]" value="{{ $cooker['basket'] ?? '' }}" class="form-control form-control-sm"></td>
                                @endforeach
                            </tr>

                            <tr class="section-header"><td colspan="{{ count($cookerData)+1 }}" class="text-left fw-bold bg-light">RICE COOKER</td></tr>
                            <tr data-field="gas"><td class="text-left">Gas ON/OFF</td>
                                @foreach($cookerData as $i => $cooker)
                                <td>
                                    <select name="cooker[{{ $i }}][gas]" class="form-control form-control-sm">
                                        <option value="ON" {{ ($cooker['gas'] ?? '') == 'ON' ? 'selected' : '' }}>ON</option>
                                        <option value="OFF" {{ ($cooker['gas'] ?? '') == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                @endforeach
                            </tr>
                            <tr data-field="waktu_masak"><td class="text-left">Waktu (Menit)</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="number" name="cooker[{{ $i }}][waktu_masak]" value="{{ $cooker['waktu_masak'] ?? '' }}" class="form-control form-control-sm" step="0.01"></td>
                                @endforeach
                            </tr>
                            <tr data-field="suhu_produk"><td class="text-left">Suhu Produk (°C)</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="number" name="cooker[{{ $i }}][suhu_produk]" value="{{ $cooker['suhu_produk'] ?? '' }}" class="form-control form-control-sm" step="0.01"></td>
                                @endforeach
                            </tr>
                            <tr data-field="suhu_after"><td class="text-left">Suhu Produk setelah 1 Menit (°C)</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="number" name="cooker[{{ $i }}][suhu_after]" value="{{ $cooker['suhu_after'] ?? '' }}" class="form-control form-control-sm" step="0.01"></td>
                                @endforeach
                            </tr>
                            <tr data-field="suhu_vacuum"><td class="text-left">Suhu After Vacuum (°C)</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="number" name="cooker[{{ $i }}][suhu_vacuum]" value="{{ $cooker['suhu_vacuum'] ?? '' }}" class="form-control form-control-sm" step="0.01"></td>
                                @endforeach
                            </tr>

                            {{-- Lama Proses --}}
                            <tr class="section-header"><td colspan="{{ count($cookerData)+1 }}" class="text-left fw-bold bg-light">LAMA PROSES</td></tr>
                            <tr data-field="jam_mulai"><td class="text-left">Jam Mulai</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="time" name="cooker[{{ $i }}][jam_mulai]" value="{{ $cooker['jam_mulai'] ?? '' }}" class="form-control form-control-sm"></td>
                                @endforeach
                            </tr>
                            <tr data-field="jam_selesai"><td class="text-left">Jam Selesai</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="time" name="cooker[{{ $i }}][jam_selesai]" value="{{ $cooker['jam_selesai'] ?? '' }}" class="form-control form-control-sm"></td>
                                @endforeach
                            </tr>

                            {{-- Sensori --}}
                            <tr class="section-header"><td colspan="{{ count($cookerData)+1 }}" class="text-left fw-bold bg-light">SENSORI</td></tr>
                            @php
                            $sensoriParams = ['kematangan'=>'Kematangan','rasa'=>'Rasa','aroma'=>'Aroma','tekstur'=>'Tekstur','warna'=>'Warna'];
                            @endphp
                            @foreach($sensoriParams as $key => $label)
                            <tr data-field="{{ $key }}"><td class="text-left">{{ $label }}</td>
                                @foreach($cookerData as $i => $cooker)
                                <td><input type="checkbox" name="cooker[{{ $i }}][sensori][{{ $key }}]" value="Oke" class="big-checkbox"
                                    {{ ($cooker['sensori'][$key] ?? '') == 'Oke' ? 'checked' : '' }}></td>
                                    @endforeach
                                </tr>
                                @endforeach

                                {{-- Aksi --}}
                                <tr data-field="aksi"><td class="text-left">Aksi</td>
                                    @foreach($cookerData as $i => $cooker)
                                    <td><button type="button" class="btn btn-danger btn-sm removeColumn">Hapus</button></td>
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
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $data->catatan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('rice.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap Select -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });

    document.addEventListener("DOMContentLoaded", function () {
        const addColumnBtn = document.getElementById("addriceColumn");
        const headerRow = document.getElementById("headerRow");
        const riceTable = document.getElementById("riceTable");
        let columnIndex = {{ count($cookerData) }};

        const fieldNames = [
          'kode_beras','berat','kode_produksi','basket','gas','waktu_masak',
          'suhu_produk','suhu_after','suhu_vacuum','jam_mulai','jam_selesai',
          'kematangan','rasa','aroma','tekstur','warna','aksi'
      ];

      addColumnBtn.addEventListener('click', function () {
        columnIndex++;
        const newHeader = document.createElement('th');
        newHeader.textContent = `Pemeriksaan ${columnIndex}`;
        headerRow.appendChild(newHeader);

        const rows = riceTable.querySelectorAll('tbody tr');
        let fieldIndex = 0;
        rows.forEach((row) => {
            if (row.classList.contains('section-header')) {
                row.querySelector('td').setAttribute('colspan', columnIndex + 1);
            } else {
                const td = document.createElement('td');
                const field = fieldNames[fieldIndex];

                if (field === 'gas') {
                    td.innerHTML = `<select name="cooker[${columnIndex - 1}][gas]" class="form-control form-control-sm">
                                            <option value="ON">ON</option>
                                            <option value="OFF">OFF</option>
                </select>`;
            } else if (['jam_mulai','jam_selesai'].includes(field)) {
                td.innerHTML = `<input type="time" name="cooker[${columnIndex - 1}][${field}]" class="form-control form-control-sm">`;
            } else if (['basket','waktu_masak'].includes(field)) {
                td.innerHTML = `<input type="number" name="cooker[${columnIndex - 1}][${field}]" class="form-control form-control-sm">`;
            } else if (['kematangan','rasa','aroma','tekstur','warna'].includes(field)) {
                td.innerHTML = `<input type="checkbox" name="cooker[${columnIndex - 1}][sensori][${field}]" value="Oke" class="big-checkbox">`;
            } else if (field === 'aksi') {
                td.innerHTML = `<button type="button" class="btn btn-danger btn-sm removeColumn">Hapus</button>`;
            } else {
                td.innerHTML = `<input type="text" name="cooker[${columnIndex - 1}][${field}]" class="form-control form-control-sm">`;
            }

            row.appendChild(td);
            fieldIndex++;
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
            riceTable.querySelectorAll('.section-header td').forEach(cell => {
                cell.setAttribute('colspan', columnIndex + 1);
            });
            headerRow.removeChild(headerRow.lastChild);
        }
    });
  });
</script>

<style>
    #riceTable th { background-color: #f8f9fa; font-weight: bold; text-align: center; }
    #riceTable td { padding: 10px; vertical-align: middle; text-align: center; }
    #riceTable tbody tr:nth-child(odd) { background-color: #f9f9f9; }
    #riceTable tbody tr:hover { background-color: #e9f7fe; }
    .form-control-sm { min-width: 120px; }
    .big-checkbox { width: 24px; height: 24px; transform: scale(1.4); cursor: pointer; }
    .section-header td { background: #f1f3f4 !important; font-weight: bold; }
</style>
@endsection

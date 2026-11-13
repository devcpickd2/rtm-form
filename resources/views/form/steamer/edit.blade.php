@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Pemasakan dengan Steamer</h4>
            <form method="POST" action="{{ route('steamer.update', $data->uuid) }}" enctype="multipart/form-data">
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
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
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

                {{-- Bagian Pemeriksaan Steamer --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Pemasakan dengan Steamer</strong>
                        <button type="button" id="addSteamerColumn" class="btn btn-primary btn-sm">
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

                        <table class="table table-bordered table-sm text-center align-middle" id="steamerTable">
                            <thead class="table-light">
                                <tr id="headerRow">
                                    <th style="min-width: 220px; text-align: left;">Parameter</th>
                                    @foreach($steamingData as $i => $steam)
                                    <th>Pemeriksaan {{ $i+1 }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                {{-- --- Parameter Utama --- --}}
                                <tr data-field="kode_produksi">
                                    <td class="text-left">Kode Produksi</td>
                                    @foreach($steamingData as $i => $steam)
                                    <td><input type="text" name="steaming[{{ $i }}][kode_produksi]"
                                        value="{{ $steam['kode_produksi'] ?? '' }}" class="form-control form-control-sm"></td>
                                        @endforeach
                                    </tr>
                                    <tr data-field="suhu_rm">
                                        <td class="text-left">T. Raw Material (°C)</td>
                                        @foreach($steamingData as $i => $steam)
                                        <td><input type="text" name="steaming[{{ $i }}][suhu_rm]"
                                            value="{{ $steam['suhu_rm'] ?? '' }}" class="form-control form-control-sm"></td>
                                            @endforeach
                                        </tr>
                                        <tr data-field="jumlah_tray">
                                            <td class="text-left">Jumlah Tray</td>
                                            @foreach($steamingData as $i => $steam)
                                            <td><input type="text" name="steaming[{{ $i }}][jumlah_tray]"
                                                value="{{ $steam['jumlah_tray'] ?? '' }}" class="form-control form-control-sm"></td>
                                                @endforeach
                                            </tr>

                                            {{-- --- STEAMING --- --}}
                                            <tr class="section-header">
                                                <td colspan="{{ count($steamingData)+1 }}" class="text-left fw-bold bg-light">STEAMING</td>
                                            </tr>
                                            <tr data-field="suhu_ruang">
                                                <td class="text-left">T. Ruang (°C)</td>
                                                @foreach($steamingData as $i => $steam)
                                                <td><input type="text" name="steaming[{{ $i }}][suhu_ruang]"
                                                    value="{{ $steam['suhu_ruang'] ?? '' }}" class="form-control form-control-sm"></td>
                                                    @endforeach
                                                </tr>
                                                <tr data-field="suhu_produk">
                                                    <td class="text-left">T. Produk (°C)</td>
                                                    @foreach($steamingData as $i => $steam)
                                                    <td><input type="text" name="steaming[{{ $i }}][suhu_produk]"
                                                        value="{{ $steam['suhu_produk'] ?? '' }}" class="form-control form-control-sm"></td>
                                                        @endforeach
                                                    </tr>
                                                    <tr data-field="suhu_after">
                                                        <td class="text-left">T. Produk setelah 1 Menit (°C)</td>
                                                        @foreach($steamingData as $i => $steam)
                                                        <td><input type="text" name="steaming[{{ $i }}][suhu_after]"
                                                            value="{{ $steam['suhu_after'] ?? '' }}" class="form-control form-control-sm"></td>
                                                            @endforeach
                                                        </tr>
                                                        <tr data-field="waktu">
                                                            <td class="text-left">Waktu (Menit)</td>
                                                            @foreach($steamingData as $i => $steam)
                                                            <td><input type="number" name="steaming[{{ $i }}][waktu]"
                                                                value="{{ $steam['waktu'] ?? '' }}" class="form-control form-control-sm"></td>
                                                                @endforeach
                                                            </tr>
                                                            <tr data-field="keterangan">
                                                                <td class="text-left">Keterangan</td>
                                                                @foreach($steamingData as $i => $steam)
                                                                <td><input type="text" name="steaming[{{ $i }}][keterangan]"
                                                                    value="{{ $steam['keterangan'] ?? '' }}" class="form-control form-control-sm"></td>
                                                                    @endforeach
                                                                </tr>

                                                                {{-- --- LAMA PROSES --- --}}
                                                                <tr class="section-header">
                                                                    <td colspan="{{ count($steamingData)+1 }}" class="text-left fw-bold bg-light">LAMA PROSES</td>
                                                                </tr>
                                                                <tr data-field="jam_mulai">
                                                                    <td class="text-left">Jam Mulai</td>
                                                                    @foreach($steamingData as $i => $steam)
                                                                    <td><input type="time" name="steaming[{{ $i }}][jam_mulai]"
                                                                        value="{{ $steam['jam_mulai'] ?? '' }}" class="form-control form-control-sm"></td>
                                                                        @endforeach
                                                                    </tr>
                                                                    <tr data-field="jam_selesai">
                                                                        <td class="text-left">Jam Selesai</td>
                                                                        @foreach($steamingData as $i => $steam)
                                                                        <td><input type="time" name="steaming[{{ $i }}][jam_selesai]"
                                                                            value="{{ $steam['jam_selesai'] ?? '' }}" class="form-control form-control-sm"></td>
                                                                            @endforeach
                                                                        </tr>

                                                                        {{-- --- SENSORI --- --}}
                                                                        <tr class="section-header">
                                                                            <td colspan="{{ count($steamingData)+1 }}" class="text-left fw-bold bg-light">SENSORI</td>
                                                                        </tr>
                                                                        @php
                                                                        $sensoriParams = ['kematangan'=>'Kematangan','rasa'=>'Rasa','aroma'=>'Aroma','tekstur'=>'Tekstur','warna'=>'Warna'];
                                                                        @endphp
                                                                        @foreach($sensoriParams as $key => $label)
                                                                        <tr data-field="{{ $key }}">
                                                                            <td class="text-left">{{ $label }}</td>
                                                                            @foreach($steamingData as $i => $steam)
                                                                            <td>
                                                                                <input type="checkbox" name="steaming[{{ $i }}][sensori][{{ $key }}]" value="Oke" class="big-checkbox"
                                                                                {{ ($steam['sensori'][$key] ?? '') == 'Oke' ? 'checked' : '' }}>
                                                                            </td>
                                                                            @endforeach
                                                                        </tr>
                                                                        @endforeach

                                                                        {{-- Aksi --}}
                                                                        <tr data-field="aksi">
                                                                            <td class="text-left">Aksi</td>
                                                                            @foreach($steamingData as $i => $steam)
                                                                            <td><button type="button" class="btn btn-danger btn-sm removeColumn">Hapus</button></td>
                                                                            @endforeach
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        {{-- Catatan --}}
                                                        <div class="card mb-4">
                                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                                <strong>Catatan</strong>

                                                                <button type="button" id="addCatatan" class="btn btn-sm btn-primary">
                                                                    + Tambah Catatan
                                                                </button>
                                                            </div>

                                                            <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <i class="bi bi-info-circle-fill me-2"></i>
                                                                    <strong> Catatan Pengisian</strong>
                                                                </div>

                                                                <ul class="mb-0 ps-3">
                                                                    <li class="mb-2">
                                                                        Isi catatan dengan rentang waktu proses pemasakan beserta keterangan penggunaan steamer.
                                                                        <br>
                                                                        <small class="text-muted">
                                                                            Contoh: 07.00 - 08.00 : Steamer digunakan untuk pemasakan daun singkong sebanyak 20 Kg.
                                                                        </small>
                                                                    </li>

                                                                    <li>
                                                                        Jika terdapat jeda penggunaan steamer sebelum digunakan kembali, tuliskan keterangan bahwa steamer tidak digunakan pada rentang waktu tersebut.
                                                                        <br>
                                                                        <small class="text-muted">
                                                                            Contoh: 08.00 - 09.00 : Tidak ada penggunaan steamer.
                                                                        </small>
                                                                    </li>
                                                                </ul>
                                                            </div>

                                                            <div class="card-body table-responsive">
                                                                <table class="table table-bordered table-sm text-center align-middle" id="catatanTable">

                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th style="width:180px;">Pukul Awal</th>
                                                                            <th style="width:180px;">Pukul Akhir</th>
                                                                            <th>Catatan</th>
                                                                            <th style="width:100px;">Aksi</th>
                                                                        </tr>
                                                                    </thead>

                                                                    <tbody>

                                                                        @forelse($catatanData as $i => $catatan)
                                                                        <tr>

                                                                            <td>
                                                                                <input type="time"
                                                                                name="catatan[{{ $i }}][pukul_awal]"
                                                                                value="{{ $catatan['pukul_awal'] ?? '' }}"
                                                                                class="form-control">
                                                                            </td>

                                                                            <td>
                                                                                <input type="time"
                                                                                name="catatan[{{ $i }}][pukul_akhir]"
                                                                                value="{{ $catatan['pukul_akhir'] ?? '' }}"
                                                                                class="form-control">
                                                                            </td>

                                                                            <td>
                                                                                <textarea
                                                                                name="catatan[{{ $i }}][isi]"
                                                                                class="form-control"
                                                                                rows="2">{{ $catatan['isi'] ?? '' }}</textarea>
                                                                            </td>

                                                                            <td>
                                                                                <button type="button"
                                                                                class="btn btn-danger btn-sm removeCatatan">
                                                                                Hapus
                                                                            </button>
                                                                        </td>

                                                                    </tr>
                                                                    @empty
                                                                    <tr>

                                                                        <td>
                                                                            <input type="time"
                                                                            name="catatan[0][pukul_awal]"
                                                                            class="form-control">
                                                                        </td>

                                                                        <td>
                                                                            <input type="time"
                                                                            name="catatan[0][pukul_akhir]"
                                                                            class="form-control">
                                                                        </td>

                                                                        <td>
                                                                            <textarea
                                                                            name="catatan[0][isi]"
                                                                            class="form-control"
                                                                            rows="2"></textarea>
                                                                        </td>

                                                                        <td>
                                                                            <button type="button"
                                                                            class="btn btn-danger btn-sm removeCatatan">
                                                                            Hapus
                                                                        </button>
                                                                    </td>

                                                                </tr>
                                                                @endforelse

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                {{-- Tombol --}}
                                                <div class="d-flex justify-content-between mt-3">
                                                    <button class="btn btn-success w-auto">
                                                        <i class="bi bi-save"></i> Update
                                                    </button>
                                                    <a href="{{ route('steamer.index') }}" class="btn btn-secondary w-auto">
                                                        <i class="bi bi-arrow-left"></i> Kembali
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap-Select -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const addColumnBtn = document.getElementById("addSteamerColumn");
        const headerRow = document.getElementById("headerRow");
        const steamerTable = document.getElementById("steamerTable");
        let columnIndex = {{ count($steamingData) }};

        const fieldNames = [
            'kode_produksi','suhu_rm','jumlah_tray','suhu_ruang','suhu_produk','suhu_after',
            'waktu','keterangan','jam_mulai','jam_selesai','kematangan','rasa','aroma','tekstur','warna','aksi'
        ];

        addColumnBtn.addEventListener('click', function () {
            columnIndex++;
            const newHeader = document.createElement('th');
            newHeader.textContent = `Pemeriksaan ${columnIndex}`;
            headerRow.appendChild(newHeader);

            const rows = steamerTable.querySelectorAll('tbody tr');
            let fieldIndex = 0;
            rows.forEach((row) => {
                if (row.classList.contains('section-header')) {
                    row.querySelector('td').setAttribute('colspan', columnIndex + 1);
                } else {
                    const td = document.createElement('td');
                    const field = row.getAttribute('data-field');
                    if (['jam_mulai','jam_selesai'].includes(field)) {
                        td.innerHTML = `<input type="time" name="steaming[${columnIndex-1}][${field}]" class="form-control form-control-sm">`;
                    } else if (field === 'waktu') {
                        td.innerHTML = `<input type="number" name="steaming[${columnIndex-1}][${field}]" class="form-control form-control-sm">`;
                    } else if (['kematangan','rasa','aroma','tekstur','warna'].includes(field)) {
                        td.innerHTML = `<input type="checkbox" name="steaming[${columnIndex-1}][sensori][${field}]" value="Oke" class="big-checkbox">`;
                    } else if (field === 'aksi') {
                        td.innerHTML = `<button type="button" class="btn btn-danger btn-sm removeColumn">Hapus</button>`;
                    } else {
                        td.innerHTML = `<input type="text" name="steaming[${columnIndex-1}][${field}]" class="form-control form-control-sm">`;
                    }
                    row.appendChild(td);
                    fieldIndex++;
                }
            });
        });

        // Hapus kolom
        steamerTable.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeColumn')) {
                const colIndex = Array.from(e.target.closest('tr').children).indexOf(e.target.closest('td'));
                steamerTable.querySelectorAll('tr').forEach(row => {
                    if (row.children[colIndex]) {
                        row.removeChild(row.children[colIndex]);
                    }
                });
                columnIndex--;
                steamerTable.querySelectorAll('.section-header td').forEach(cell => {
                    cell.setAttribute('colspan', columnIndex + 1);
                });
                headerRow.removeChild(headerRow.lastChild);
            }
        });
    });
</script>

<style>
    #steamerTable th { background-color: #f8f9fa; font-weight: bold; text-align: center; }
    #steamerTable td { padding: 10px; vertical-align: middle; text-align: center; }
    #steamerTable tbody tr:nth-child(odd) { background-color: #f9f9f9; }
    #steamerTable tbody tr:hover { background-color: #e9f7fe; }
    .form-control-sm { min-width: 120px; }
    .big-checkbox { width: 24px; height: 24px; transform: scale(1.4); cursor: pointer; }
    .section-header td { background: #f1f3f4 !important; font-weight: bold; }
    #catatanTable td {
        vertical-align: middle;
    }

    #catatanTable input[type="time"] {
        text-align: center;
    }

    #catatanTable input[type="time"]::-webkit-datetime-edit {
        width: 100%;
        display: flex;
        justify-content: center;
    }
</style>

<script>

    let catatanIndex = {{ count($catatanData) > 0 ? count($catatanData) : 1 }};

    document.getElementById('addCatatan').addEventListener('click', function () {

        const tbody = document.querySelector('#catatanTable tbody');

        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td>
                <input type="time"
                    name="catatan[${catatanIndex}][pukul_awal]"
                    class="form-control">
            </td>

            <td>
                <input type="time"
                    name="catatan[${catatanIndex}][pukul_akhir]"
                    class="form-control">
            </td>

            <td>
                <textarea
                    name="catatan[${catatanIndex}][isi]"
                    class="form-control"
                    rows="2"></textarea>
            </td>

            <td>
                <button type="button"
                    class="btn btn-danger btn-sm removeCatatan">
                    Hapus
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        catatanIndex++;
    });

    document.addEventListener('click', function(e){

        if(e.target.classList.contains('removeCatatan')){

            e.target.closest('tr').remove();
        }
    });

</script>
@endsection

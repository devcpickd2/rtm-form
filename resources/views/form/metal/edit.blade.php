@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Metal Detector</h4>
            <form method="POST" action="{{ route('metal.update', $metal->uuid) }}" enctype="multipart/form-data">
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
                                <input type="date" id="dateInput" name="date" class="form-control" value="{{ old('date', $metal->date ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $metal->shift ?? '') == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $metal->shift ?? '') == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $metal->shift ?? '') == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $metal->nama_produk ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" value="{{ old('kode_produksi', $metal->kode_produksi ?? '') }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">No. Program</label>
                                <input type="text" id="no_program" name="no_program" class="form-control" value="{{ old('no_program', $metal->no_program ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan Metal --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Pemeriksaan Metal Detector</strong>
                        <button type="button" id="addpemeriksaanRow" class="btn btn-primary btn-sm">+ Tambah Baris</button>
                    </div>
                    <div class="card-body table-responsive" style="overflow-x:auto;">
                        <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>  
                            <i class="bi bi-check-circle text-success"></i> Checkbox apabila hasil <u>Oke</u>. Kosongkan Checkbox apabila hasil <u>Tidak Oke</u>.
                        </div>

                        <table class="table table-bordered table-sm text-center align-middle" id="pemeriksaanTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Pukul</th>
                                    <th>Fe (1.5)</th>
                                    <th>Non Fe (2.0)</th>
                                    <th>SUS 316 (2.5)</th>
                                    <th>Keterangan</th>
                                    <th>Tindakan Koreksi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="pemeriksaan">
                                @php
                                    $pemeriksaanData = $pemeriksaanData ?? [['pukul'=>'','fe'=>'','non_fe'=>'','sus_316'=>'','keterangan'=>'Terdeteksi','tindakan_koreksi'=>'']];
                                @endphp
                                @foreach($pemeriksaanData as $index => $row)
                                <tr>
                                    <td>
                                        <input type="time" name="pemeriksaan[{{ $index }}][pukul]" class="form-control form-control-sm" value="{{ old("pemeriksaan.$index.pukul", $row['pukul'] ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="form-check text-center m-0">
                                            <input type="checkbox" name="pemeriksaan[{{ $index }}][fe]" class="form-check-input" value="Oke" {{ old("pemeriksaan.$index.fe", $row['fe'] ?? '') == 'Oke' ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check text-center m-0">
                                            <input type="checkbox" name="pemeriksaan[{{ $index }}][non_fe]" class="form-check-input" value="Oke" {{ old("pemeriksaan.$index.non_fe", $row['non_fe'] ?? '') == 'Oke' ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check text-center m-0">
                                            <input type="checkbox" name="pemeriksaan[{{ $index }}][sus_316]" class="form-check-input" value="Oke" {{ old("pemeriksaan.$index.sus_316", $row['sus_316'] ?? '') == 'Oke' ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="pemeriksaan[{{ $index }}][keterangan]" class="form-control form-control-sm" required>
                                            <option value="Terdeteksi" {{ old("pemeriksaan.$index.keterangan", $row['keterangan'] ?? 'Terdeteksi') == 'Terdeteksi' ? 'selected' : '' }}>Terdeteksi</option>
                                            <option value="Tidak Terdeteksi" {{ old("pemeriksaan.$index.keterangan", $row['keterangan'] ?? 'Terdeteksi') == 'Tidak Terdeteksi' ? 'selected' : '' }}>Tidak Terdeteksi</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="pemeriksaan[{{ $index }}][tindakan_koreksi]" class="form-control form-control-sm" value="{{ old("pemeriksaan.$index.tindakan_koreksi", $row['tindakan_koreksi'] ?? '') }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $metal->catatan ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Tombol Simpan --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Simpan</button>
                    <a href="{{ route('metal.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery & Bootstrap Select -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById('pemeriksaanTable');
    const addBtn = document.getElementById('addpemeriksaanRow');

    addBtn.addEventListener('click', function () {
        const lastTbody = table.querySelector('tbody:last-of-type');
        const clone = lastTbody.querySelector('tr').cloneNode(true);

        let index = table.querySelectorAll('tbody tr').length;

        clone.querySelectorAll('input').forEach(function(el) {
            if(el.name) el.name = el.name.replace(/\[\d+\]/, '['+index+']');
            if(el.type === 'checkbox') el.checked = false;
            else el.value = '';
        });

        clone.querySelector('select').name = `pemeriksaan[${index}][keterangan]`;
        clone.querySelector('select').selectedIndex = 0;

        clone.querySelector('.removeRow').addEventListener('click', function() {
            clone.remove();
        });

        table.querySelector('tbody').appendChild(clone);
    });

    table.querySelectorAll('.removeRow').forEach(btn => {
        btn.addEventListener('click', function() {
            btn.closest('tr').remove();
        });
    });
});
</script>

<style>
    .form-control-sm { min-width: 120px; }
    .form-check-input { width: 20px; height: 20px; margin: 0 auto; }
    .form-check { display: flex; justify-content: center; align-items: center; height: 100%; }
    .table-bordered th, .table-bordered td { text-align: center; vertical-align: middle; }
</style>
@endsection

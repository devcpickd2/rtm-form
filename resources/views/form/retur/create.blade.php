@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Produk Retur</h4>
            <form method="POST" action="{{ route('retur.store') }}">
                @csrf
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">No. Mobil</label>
                                <input type="text" id="no_mobil" name="no_mobil" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Supir</label>
                                <input type="text" id="nama_supir" name="nama_supir" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Pemeriksaan Retur</strong>
                    </div>
                    <div class="card-body">
                        {{-- Note Petunjuk Checkbox --}}
                        <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>  
                            <i class="bi bi-check-circle text-success"></i>  Checkbox apabila hasil <u>Sesuai</u>.  
                            Kosongkan Checkbox apabila hasil <u>Tidak Sesuai</u>.  
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk"
                                class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}"
                                    {{ old('nama_produk', $data->nama_produk ?? '') == $produk->nama_produk ? 'selected' : '' }}>
                                    {{ $produk->nama_produk }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Produksi</label>
                            <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                     <div class="col-md-6">
                        <label class="form-label">Expired Date</label>
                        <input type="date" id="expired_date" name="expired_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jumlah (Box/Pack)</label>
                        <input type="number" id="jumlah" name="jumlah" class="form-control">
                    </div>
                </div>

                {{-- Tambahkan CSS ini di section <style> atau file CSS kamu --}}
                <style>
                .table {
                    table-layout: fixed;
                    width: 100%;
                    border-collapse: collapse; 
                }
                .table th, .table td {
                    text-align: center;
                    vertical-align: middle;
                    border: none; 
                }

                .big-checkbox {
                    width:35px;
                    height: 25px;
                    transform: scale(1.3);
                    cursor: pointer;
                    accent-color: #198754;
                }
            </style>

            <div class="table-responsive">
             <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" colspan="3">KESESUAIAN *</th>
                    </tr>
                    <tr>
                        <th>Bocor</th>
                        <th>Isi Kurang</th>
                        <th>Lainnya</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">
                            <input type="hidden" name="bocor" value="tidak sesuai">
                            <input class="form-check-input big-checkbox" type="checkbox" name="bocor" value="sesuai"
                            {{ old('bocor', $retur->bocor ?? '') === 'sesuai' ? 'checked' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="hidden" name="isi_kurang" value="tidak sesuai">
                            <input class="form-check-input big-checkbox" type="checkbox" name="isi_kurang" value="sesuai"
                            {{ old('isi_kurang', $retur->isi_kurang ?? '') === 'sesuai' ? 'checked' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="hidden" name="lainnya" value="tidak sesuai">
                            <input class="form-check-input big-checkbox" type="checkbox" name="lainnya" value="sesuai"
                            {{ old('lainnya', $retur->lainnya ?? '') === 'sesuai' ? 'checked' : '' }}>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-light">
        <strong>Keterangan</strong>
    </div>
    <div class="card-body">
        <div class="alert alert-warning mt-2 py-2 px-3" style="font-size: 0.9rem;">
            <i class="bi bi-info-circle"></i>
            <strong>Keterangan:</strong>  
            Untuk Kolom <u>Keterangan</u> isi dengan jumlah produk sesuai dengan kondisinya.  
        </div>
        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan keterangan"></textarea>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-light">
        <strong>Catatan</strong>
    </div>
    <div class="card-body">
        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada"></textarea>
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
            <option value="Fikri">Fikri</option>
            <option value="Cahyo">Cahyo</option>
            <option value="Renaldi">Renaldi</option>
        </select>
    </div>
</div>

<div class="d-flex justify-content-between mt-3">
    <button class="btn btn-success w-auto">
        <i class="bi bi-save"></i> Simpan
    </button>
    <a href="{{ route('retur.index') }}" class="btn btn-secondary w-auto">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

</form>
</div>
</div>
</div>

{{-- jQuery dulu (wajib) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Bootstrap-Select CSS & JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function () {
        $('.selectpicker').selectpicker();
    });

    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");

        // Ambil waktu sekarang
        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');

        // Set value tanggal
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
@endsection

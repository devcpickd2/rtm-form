@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Verifikasi Pengemasan</h4>
            <form method="POST" action="{{ route('pengemasan.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}">
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
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="alert alert-warning mt-2 py-2 px-3" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle"></i>
                    <strong>Catatan:</strong>
                    Khusus produk RTE periksa kondisi hasil sealing (tidak miring, tidak melipat, minimal lebar seal 1 cm) dan kondisi pouch tidak bocor. Tuliskan hasil pemeriksaan di kolom <u>Keterangan</u>.
                </div>

                <div class="alert alert-danger mt-2 py-2 px-3" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle"></i>
                    <strong>Catatan:</strong>
                    Upload gambar pada Kode Produksi dan Best Before untuk bukti saat melakukan checking atau packing.
                </div>

                {{-- ========================= --}}
                {{-- PENGEMASAN - CHECKING --}}
                {{-- ========================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white text-center">
                        <strong>PENGEMASAN - CHECKING</strong>
                    </div>

                    <div class="card-body">

                        {{-- Info Waktu --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" required>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pukul</label>
                                <input type="time" name="pukul" class="form-control" required>
                            </div>
                        </div>

                        {{-- TRAY / PACK CHECKING --}}
                        <h6 class="fw-bold mb-2 mt-1 text-primary"><b>Tray / Pack Checking</b></h6>
                        <table class="table table-bordered table-sm text-center align-middle mb-4 shadow-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Prod. Code / Best Before</th>
                                    <th>QR Code</th>
                                    <th>Kondisi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td><input type="text" name="tray_checking[nama_produk]" class="form-control form-control-sm"></td>

                                    <td>
                                        <input type="file" name="tray_checking[kode_produksi]"
                                        class="form-control form-control-sm @error('tray_checking.kode_produksi') is-invalid @enderror"
                                        accept="image/*">
                                        @error('tray_checking.kode_produksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td>
                                        <select name="tray_checking[qrcode]" class="form-control form-control-sm">
                                            <option value="sesuai">Sesuai</option>
                                            <option value="tidak sesuai">Tidak Sesuai</option>
                                        </select>
                                    </td>

                                    <td>
                                        <select name="tray_checking[kondisi]" class="form-control form-control-sm">
                                            <option value="oke">Oke</option>
                                            <option value="tidak oke">Tidak Oke</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- BOX CHECKING --}}
                        <h6 class="fw-bold mb-2 text-primary"><b>Box Checking</b></h6>
                        <table class="table table-bordered table-sm text-center align-middle shadow-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk / Prod. Code / Best Before</th>
                                    <th>Kondisi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        <input type="file" name="box_checking[kode_produksi]"
                                        class="form-control form-control-sm @error('box_checking.kode_produksi') is-invalid @enderror"
                                        accept="image/*">
                                        @error('box_checking.kode_produksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td>
                                        <select name="box_checking[kondisi]" class="form-control form-control-sm">
                                            <option value="oke">Oke</option>
                                            <option value="tidak oke">Tidak Oke</option>
                                        </select>
                                    </td>

                                    <td><input type="text" name="keterangan_checking" class="form-control form-control-sm"></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

                <hr class="my-4">

                {{-- ========================= --}}
                {{-- PENGEMASAN - PACKING --}}
                {{-- ========================= --}}
                <div class="card mb-4">
                    <div class="card-header bg-success text-white text-center">
                        <strong>PENGEMASAN - PACKING</strong>
                    </div>

                    <div class="card-body">

                        {{-- Info Waktu --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date_packing" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select name="shift_packing" class="form-control">
                                    <option value="">Pilih Shift</option>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pukul</label>
                                <input type="time" name="pukul_packing" class="form-control">
                            </div>
                        </div>

                        {{-- TRAY / PACK PACKING --}}
                        <h6 class="fw-bold mb-2 text-success"><b>Tray / Pack Packing</b></h6>
                        <table class="table table-bordered table-sm text-center align-middle mb-4 shadow-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Prod. Code / Best Before</th>
                                    <th>QR Code</th>
                                    <th>Kondisi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td><input type="text" name="tray_packing[nama_produk]" class="form-control form-control-sm"></td>

                                    <td>
                                        <input type="file" name="tray_packing[kode_produksi]"
                                        class="form-control form-control-sm @error('tray_packing.kode_produksi') is-invalid @enderror"
                                        accept="image/*">
                                        @error('tray_packing.kode_produksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td>
                                        <select name="tray_packing[qrcode]" class="form-control form-control-sm">
                                            <option value="">Pilihan</option>
                                            <option value="sesuai">Sesuai</option>
                                            <option value="tidak sesuai">Tidak Sesuai</option>
                                        </select>
                                    </td>

                                    <td>
                                        <select name="tray_packing[kondisi]" class="form-control form-control-sm">
                                            <option value="">Pilihan</option>
                                            <option value="oke">Oke</option>
                                            <option value="tidak oke">Tidak Oke</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- BOX PACKING --}}
                        <h6 class="fw-bold mb-2 text-success"><b>Box Packing</b></h6>
                        <table class="table table-bordered table-sm text-center align-middle shadow-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk / Prod. Code / Best Before</th>
                                    <th>Isi Box</th>
                                    <th>Kondisi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        <input type="file" name="box_packing[kode_produksi]"
                                        class="form-control form-control-sm @error('box_packing.kode_produksi') is-invalid @enderror"
                                        accept="image/*">
                                        @error('box_packing.kode_produksi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td><input type="number" name="box_packing[isi_box]" class="form-control form-control-sm"></td>

                                    <td>
                                        <select name="box_packing[kondisi]" class="form-control form-control-sm">
                                            <option value="">Pilihan</option>
                                            <option value="oke">Oke</option>
                                            <option value="tidak oke">Tidak Oke</option>
                                        </select>
                                    </td>

                                    <td><input type="text" name="keterangan_packing" class="form-control form-control-sm"></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Catatan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada"></textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('pengemasan.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- jQuery & bootstrap-select --}}
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
    $('#nama_produk').on('change', function () {
      let selectedNama = $(this).val();
      $('input[name="tray_checking[nama_produk]"]').val(selectedNama);
  });
</script>
<script>
    $(document).ready(function() {
        function setShift(hour) {
            if (hour >= 7 && hour < 15) return "1";
            else if (hour >= 15 && hour < 23) return "2";
            else return "3";
        }

        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const hh = String(now.getHours()).padStart(2, '0');
        const min = String(now.getMinutes()).padStart(2, '0');

    // Checking
        const date = $('input[name="date"]');
        const time = $('input[name="pukul"]');
        const shift = $('select[name="shift"]');
        if(date.length) date.val(`${yyyy}-${mm}-${dd}`);
        if(time.length) time.val(`${hh}:${min}`);
        if(shift.length) shift.val(setShift(parseInt(hh)));

    });
</script>

<style>
    .table-pengemasan {
        min-width: 1800px;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-pengemasan th,
    .table-pengemasan td {
        min-width: 150px;
        padding: 1rem;
    }
    .table-pengemasan .separator {
        border-right: 1px solid grey;
    }
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
    }
</style>
@endsection

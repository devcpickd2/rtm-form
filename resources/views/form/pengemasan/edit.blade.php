@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Verifikasi Pengemasan</h4>

            <form method="POST" action="{{ route('pengemasan.update', $pengemasan->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">

                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" 
                                class="form-control selectpicker" data-live-search="true" required>
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}"
                                    {{ $pengemasan->nama_produk == $produk->nama_produk ? 'selected' : '' }}>
                                    {{ $produk->nama_produk }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kode Produksi</label>
                            <input type="text" id="kode_produksi" name="kode_produksi"
                            class="form-control"
                            value="{{ $pengemasan->kode_produksi }}" required>
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
                            <input type="date" name="date" class="form-control"
                            value="{{ $pengemasan->date }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Shift</label>
                            <select name="shift" class="form-control">
                                <option value="1" {{ $pengemasan->shift == '1' ? 'selected' : '' }}>Shift 1</option>
                                <option value="2" {{ $pengemasan->shift == '2' ? 'selected' : '' }}>Shift 2</option>
                                <option value="3" {{ $pengemasan->shift == '3' ? 'selected' : '' }}>Shift 3</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Pukul</label>
                            <input type="time" name="pukul" class="form-control"
                            value="{{ $pengemasan->pukul }}">
                        </div>
                    </div>

                    {{-- TRAY CHECKING --}}
                    <h6 class="fw-bold text-primary">Tray / Pack Checking</h6>
                    <table class="table table-bordered table-sm text-center align-middle mb-4">
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
                                <td>
                                    <input type="text"
                                    name="tray_checking[nama_produk]"
                                    class="form-control form-control-sm"
                                    value="{{ $pengemasan->tray_checking['nama_produk'] ?? '' }}">
                                </td>

                                <td>
                                 <input type="file" 
                                 name="tray_checking[kode_produksi]"
                                 class="form-control form-control-sm"
                                 accept="image/*">
                                 @if(isset($pengemasan->tray_checking['kode_produksi']))
                                 <a href="{{ asset('storage/' . $pengemasan->tray_checking['kode_produksi']) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $pengemasan->tray_checking['kode_produksi']) }}"
                                    class="img-thumbnail mb-2" width="120">
                                </a>
                                @endif
                            </td>

                            <td>
                                <select name="tray_checking[qrcode]" class="form-control form-control-sm">
                                    <option value="sesuai" 
                                    {{ ($pengemasan->tray_checking['qrcode'] ?? '') == 'sesuai' ? 'selected' : '' }}>
                                    Sesuai
                                </option>
                                <option value="tidak sesuai"
                                {{ ($pengemasan->tray_checking['qrcode'] ?? '') == 'tidak sesuai' ? 'selected' : '' }}>
                                Tidak Sesuai
                            </option>
                        </select>
                    </td>

                    <td>
                        <select name="tray_checking[kondisi]" class="form-control form-control-sm">
                            <option value="oke"
                            {{ ($pengemasan->tray_checking['kondisi'] ?? '') == 'oke' ? 'selected' : '' }}>
                            Oke
                        </option>
                        <option value="tidak oke"
                        {{ ($pengemasan->tray_checking['kondisi'] ?? '') == 'tidak oke' ? 'selected' : '' }}>
                        Tidak Oke
                    </option>
                </select>
            </td>

        </tr>
    </tbody>
</table>

{{-- BOX CHECKING --}}
<h6 class="fw-bold text-primary">Box Checking</h6>
<table class="table table-bordered table-sm text-center align-middle">
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
                <input type="file" 
                name="box_checking[kode_produksi]"
                class="form-control form-control-sm"
                accept="image/*">
                @if(isset($pengemasan->box_checking['kode_produksi']))
                <a href="{{ asset('storage/' . $pengemasan->box_checking['kode_produksi']) }}" target="_blank">
                    <img src="{{ asset('storage/' . $pengemasan->box_checking['kode_produksi']) }}"
                    class="img-thumbnail mb-2" width="120">
                </a>
                @endif
            </td>

            <td>
                <select name="box_checking[kondisi]" class="form-control form-control-sm">
                    <option value="oke" {{ ($pengemasan->box_checking['kondisi'] ?? '') == 'oke' ? 'selected' : '' }}>Oke</option>
                    <option value="tidak oke" {{ ($pengemasan->box_checking['kondisi'] ?? '') == 'tidak oke' ? 'selected' : '' }}>Tidak Oke</option>
                </select>
            </td>

            <td>
                <input type="text" name="keterangan_checking"
                class="form-control form-control-sm"
                value="{{ $pengemasan->keterangan_checking }}">
            </td>
        </tr>
    </tbody>
</table>

</div>
</div>
<hr>

{{-- ========================= --}}
{{-- PENGEMASAN - PACKING --}}
{{-- ========================= --}}
<div class="card mb-4">
    <div class="card-header bg-success text-white text-center">
        <strong>PENGEMASAN - PACKING</strong>
    </div>

    <div class="card-body">

        {{-- Info waktu --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date_packing" class="form-control"
                value="{{ $pengemasan->date_packing }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Shift</label>
                <select name="shift_packing" class="form-control">
                    <option value="">Pilih Shift</option>
                    <option value="1" {{ $pengemasan->shift_packing == '1' ? 'selected' : '' }}>Shift 1</option>
                    <option value="2" {{ $pengemasan->shift_packing == '2' ? 'selected' : '' }}>Shift 2</option>
                    <option value="3" {{ $pengemasan->shift_packing == '3' ? 'selected' : '' }}>Shift 3</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Pukul</label>
                <input type="time" name="pukul_packing" class="form-control"
                value="{{ $pengemasan->pukul_packing }}">
            </div>
        </div>

        {{-- TRAY PACKING --}}
        <h6 class="fw-bold text-success">Tray / Pack Packing</h6>
        <table class="table table-bordered table-sm text-center align-middle mb-4">

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

                    <td>
                        <input type="text"
                        name="tray_packing[nama_produk]"
                        class="form-control form-control-sm"
                        value="{{ $pengemasan->tray_packing['nama_produk'] ?? '' }}">
                    </td>

                    <td>
                        <input type="file" 
                        name="tray_packing[kode_produksi]"
                        class="form-control form-control-sm"
                        accept="image/*">
                        @if(isset($pengemasan->tray_packing['kode_produksi']))
                        <a href="{{ asset('storage/' . $pengemasan->tray_packing['kode_produksi']) }}" target="_blank">
                            <img src="{{ asset('storage/' . $pengemasan->tray_packing['kode_produksi']) }}"
                            class="img-thumbnail mb-2" width="120">
                        </a>
                        @endif
                    </td>

                    <td>
                        <select name="tray_packing[qrcode]" class="form-control form-control-sm">
                            <option value="">Pilihan</option>
                            <option value="sesuai" 
                            {{ ($pengemasan->tray_packing['qrcode'] ?? '') == 'sesuai' ? 'selected' : '' }}>
                            Sesuai
                        </option>
                        <option value="tidak sesuai"
                        {{ ($pengemasan->tray_packing['qrcode'] ?? '') == 'tidak sesuai' ? 'selected' : '' }}>
                        Tidak Sesuai
                    </option>
                </select>
            </td>

            <td>
                <select name="tray_packing[kondisi]" class="form-control form-control-sm">
                    <option value="">Pilihan</option>
                    <option value="oke"
                    {{ ($pengemasan->tray_packing['kondisi'] ?? '') == 'oke' ? 'selected' : '' }}>
                    Oke
                </option>
                <option value="tidak oke"
                {{ ($pengemasan->tray_packing['kondisi'] ?? '') == 'tidak oke' ? 'selected' : '' }}>
                Tidak Oke
            </option>
        </select>
    </td>

</tr>
</tbody>

</table>

{{-- BOX PACKING --}}
<h6 class="fw-bold text-success">Box Packing</h6>
<table class="table table-bordered table-sm text-center align-middle">

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
                <input type="file" 
                name="box_packing[kode_produksi]"
                class="form-control form-control-sm"
                accept="image/*">
                @if(isset($pengemasan->box_packing['kode_produksi']))
                <a href="{{ asset('storage/' . $pengemasan->box_packing['kode_produksi']) }}" target="_blank">
                    <img src="{{ asset('storage/' . $pengemasan->box_packing['kode_produksi']) }}"
                    class="img-thumbnail mb-2" width="120">
                </a>
                @endif
            </td>

            <td>
                <input type="number"
                name="box_packing[isi_box]"
                class="form-control form-control-sm"
                value="{{ $pengemasan->box_packing['isi_box'] ?? '' }}">
            </td>

            <td>
                <select name="box_packing[kondisi]" class="form-control form-control-sm">
                    <option value="">Pilihan</option>
                    <option value="oke"
                    {{ ($pengemasan->box_packing['kondisi'] ?? '') == 'oke' ? 'selected' : '' }}>
                    Oke
                </option>
                <option value="tidak oke"
                {{ ($pengemasan->box_packing['kondisi'] ?? '') == 'tidak oke' ? 'selected' : '' }}>
                Tidak Oke
            </option>
        </select>
    </td>

    <td>
        <input type="text" name="keterangan_packing"
        class="form-control form-control-sm"
        value="{{ $pengemasan->keterangan_packing }}">
    </td>

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
        <textarea name="catatan" class="form-control" rows="3">{{ $pengemasan->catatan }}</textarea>
    </div>
</div>

{{-- Tombol --}}
<div class="d-flex justify-content-between mt-3">
    <button class="btn btn-success">
        <i class="bi bi-save"></i> Update
    </button>

    <a href="{{ route('pengemasan.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

</form>
</div>
</div>
</div>

{{-- jQuery --}}
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

@endsection

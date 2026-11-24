@extends('layouts.app')

@section('content')

@php
    $auditRecord = $pengemasan ?? null;
    $isAuditMode = (bool) ($auditRecord->is_audit ?? false);
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Verifikasi Pengemasan
                    <span class="badge audit-badge ms-2" @if(!$isAuditMode) style="display:none" @endif>MODE AUDIT</span>
                </h4>
@if($isAuditMode)
    <div class="audit-edit-context mb-4">
        <div class="audit-breadcrumb mb-3">
            <a href="{{ route('pengemasan.audit') }}">
                <i class="bi bi-arrow-left me-1"></i> Data Audit
            </a>
            <span class="mx-2">/</span>
            <span class="fw-bold">Edit Data Audit</span>
        </div>

        <div class="alert audit-info d-flex align-items-start mb-0" role="alert">
            <i class="bi bi-shield-check me-3 fs-4"></i>
            <div>
                <strong class="d-block mb-1">Mode Audit</strong>
                <div class="small">
                    Anda sedang mengubah snapshot Audit. Perubahan disimpan pada data Audit ini saja
                    dan tidak mengubah data operasional asli.
                </div>
            </div>
        </div>
    </div>
@endif


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


<style>
    .audit-badge { background:#7c3aed; color:#fff; font-size:.65rem; font-weight:700; letter-spacing:.5px; padding:6px 10px; border-radius:5px; vertical-align:middle; }
    .audit-breadcrumb { background:#dc3545; border:1px solid #dc3545; border-radius:8px; padding:11px 15px; font-size:.88rem; color:#fff; box-shadow:0 2px 5px rgba(220,53,69,.15); }
    .audit-breadcrumb a { color:#fff; font-weight:600; text-decoration:none; transition:.2s ease; }
    .audit-breadcrumb a:hover { color:#ffe3e6; text-decoration:underline; }
    .audit-breadcrumb span { color:#fff !important; }
    .audit-info { background:#f5f0ff; border:1px solid #e9d5ff; border-left:4px solid #7c3aed; border-radius:8px; color:#4c1d95; padding:14px 16px; }
    .audit-info i { color:#7c3aed; }
    .audit-info strong { color:#6b21a8; font-size:.95rem; }
</style>

@endsection

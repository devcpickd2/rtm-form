@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            @if($isAudit)
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('pemusnahan.audit') }}"><i class="bi bi-arrow-left me-1"></i>Data Audit</a>
                    <span class="mx-2">/</span>
                    <span class="fw-bold">Edit Data Audit</span>
                </div>
                <div class="alert audit-info d-flex align-items-start mb-4" role="alert">
                    <i class="bi bi-shield-check me-3 fs-4"></i>
                    <div><strong class="d-block mb-1">Halaman Edit Data Audit</strong>
                        <div class="small">Anda sedang mengubah snapshot data Audit. Perubahan hanya berlaku pada data Audit dan tidak mengubah data operasional asli.</div>
                    </div>
                </div>
            @endif 
            <h4><i class="bi bi-pencil-square"></i> Form Edit Pemusnahan Barang / Produk@if($isAudit) <span class="badge audit-badge ms-2">MODE AUDIT</span> @endif</h4>
            <form method="POST" action="{{ route('pemusnahan.update', $pemusnahan->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- penting untuk update --}}

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" 
                                    value="{{ old('date', $pemusnahan->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}"
                                        {{ old('nama_produk', $pemusnahan->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" 
                                    value="{{ old('kode_produksi', $pemusnahan->kode_produksi) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expired Date</label>
                                <input type="date" id="expired_date" name="expired_date" class="form-control" 
                                    value="{{ old('expired_date', $pemusnahan->expired_date) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Analisis Masalah</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="analisis" class="form-control" placeholder="Tuliskan Analisis Masalah">{{ old('analisis', $pemusnahan->analisis) }}</textarea>
                    </div>
                    <div class="card-header bg-light">
                        <strong>Keterangan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="keterangan" class="form-control" placeholder="Tuliskan keterangan">{{ old('keterangan', $pemusnahan->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route($isAudit ? 'pemusnahan.audit' : 'pemusnahan.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap-Select CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>


<style>
.audit-breadcrumb{background:#dc3545;border:1px solid #dc3545;border-radius:8px;padding:11px 15px;font-size:.88rem;color:#fff;box-shadow:0 2px 5px rgba(220,53,69,.15)}
.audit-breadcrumb a{color:#fff;font-weight:600;text-decoration:none;transition:.2s ease}.audit-breadcrumb a:hover{color:#ffe3e6;text-decoration:underline}.audit-breadcrumb span{color:#fff!important}
.audit-info{background:#f5f0ff;border:1px solid #e9d5ff;border-left:4px solid #7c3aed;border-radius:8px;color:#4c1d95;padding:14px 16px}.audit-info i{color:#7c3aed}.audit-info strong{color:#6b21a8;font-size:.95rem}
.audit-badge{background:#7c3aed;color:#fff;font-size:.65rem;font-weight:700;letter-spacing:.5px;padding:6px 10px;border-radius:5px;vertical-align:middle}
</style>

@endsection

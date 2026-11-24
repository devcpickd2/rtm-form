@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            @if($isAudit)
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('memo.audit') }}"><i class="bi bi-arrow-left me-1"></i>Data Audit</a>
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
            <h4><i class="bi bi-pencil-square"></i> Edit Memo Retur@if($isAudit) <span class="badge audit-badge ms-2">MODE AUDIT</span> @endif</h4>

            <form method="POST" action="{{ route('memo.update', $memo->uuid) }}">
                @csrf
                @method('PUT')

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Memo</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input
                                    type="date"
                                    name="date"
                                    class="form-control"
                                    value="{{ old('date', \Carbon\Carbon::parse($memo->date)->format('Y-m-d')) }}"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $memo->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $memo->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $memo->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Memo Retur --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Memo Retur</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">From</label>
                                <input
                                    type="text"
                                    name="retur_dari"
                                    class="form-control"
                                    value="{{ old('retur_dari', $memo->retur_dari) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">To</label>
                                <input
                                    type="text"
                                    name="retur_ke"
                                    class="form-control"
                                    value="{{ old('retur_ke', $memo->retur_ke) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Subject</label>
                                <input
                                    type="text"
                                    name="subject"
                                    class="form-control"
                                    value="{{ old('subject', $memo->subject) }}">
                            </div>
                        </div>

                        <div class="alert alert-info mt-2 py-2 px-3" style="font-size:0.9rem;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong>
                            Masukkan detail retur seperti produk apa yang diretur, jumlah kedatangan dan tanggal kedatangan barang, alasan retur dan jumlah produk yang diretur.
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Detail Retur</label>

                                <textarea
                                    id="detail_retur"
                                    name="detail_retur"
                                    class="form-control"
                                    rows="10">{{ old('detail_retur', $memo->detail_retur) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Update
                    </button>

                    <a href="{{ route($isAudit ? 'memo.audit' : 'memo.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
.audit-breadcrumb{background:#dc3545;border:1px solid #dc3545;border-radius:8px;padding:11px 15px;font-size:.88rem;color:#fff;box-shadow:0 2px 5px rgba(220,53,69,.15)}
.audit-breadcrumb a{color:#fff;font-weight:600;text-decoration:none;transition:.2s ease}.audit-breadcrumb a:hover{color:#ffe3e6;text-decoration:underline}.audit-breadcrumb span{color:#fff!important}
.audit-info{background:#f5f0ff;border:1px solid #e9d5ff;border-left:4px solid #7c3aed;border-radius:8px;color:#4c1d95;padding:14px 16px}.audit-info i{color:#7c3aed}.audit-info strong{color:#6b21a8;font-size:.95rem}
.audit-badge{background:#7c3aed;color:#fff;font-size:.65rem;font-weight:700;letter-spacing:.5px;padding:6px 10px;border-radius:5px;vertical-align:middle}
</style>

@endsection
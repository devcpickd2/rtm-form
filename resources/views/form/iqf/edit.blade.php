@extends('layouts.app')

@section('content')

@php
    $auditRecord = $iqf ?? null;
    $isAuditMode = (bool) ($auditRecord->is_audit ?? false);
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4><i class="bi bi-pencil-square"></i> Form Edit Pengecekan Suhu Produk Setiap IQF Proses
                    <span class="badge audit-badge ms-2" @if(!$isAuditMode) style="display:none" @endif>MODE AUDIT</span>
                </h4>
@if($isAuditMode)
    <div class="audit-edit-context mb-4">
        <div class="audit-breadcrumb mb-3">
            <a href="{{ route('iqf.audit') }}">
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


            <form method="POST" action="{{ route('iqf.update', $iqf->uuid) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- IDENTITAS --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white"><strong>Identitas Pemeriksaan</strong></div>
                    <div class="card-body">

                        <div class="row mb-3">
                            {{-- TANGGAL --}}
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control"
                                value="{{ old('date', $iqf->date) }}" required>

                                @error('date')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div> 

                            {{-- SHIFT --}}
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift',$iqf->shift)=='1'?'selected':'' }}>Shift 1</option>
                                    <option value="2" {{ old('shift',$iqf->shift)=='2'?'selected':'' }}>Shift 2</option>
                                    <option value="3" {{ old('shift',$iqf->shift)=='3'?'selected':'' }}>Shift 3</option>
                                </select>

                                @error('shift')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- IQF NO --}}
                            <div class="col-md-4">
                                <label class="form-label">IQF No.</label>
                                <input type="text" name="no_iqf" class="form-control"
                                value="{{ old('no_iqf',$iqf->no_iqf) }}">

                                @error('no_iqf')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- NAMA PRODUK --}}
                            <div class="col-md-4">
                                <label class="form-label">Nama Produk</label>
                                <select id="nama_produk" name="nama_produk" class="form-control selectpicker"
                                data-live-search="true" required>
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}"
                                    {{ old('nama_produk',$iqf->nama_produk)==$produk->nama_produk?'selected':'' }}>
                                    {{ $produk->nama_produk }}
                                </option>
                                @endforeach
                            </select>

                            @error('nama_produk')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- KODE PRODUKSI --}}
                        <div class="col-md-4">
                            <label class="form-label">Kode Produksi</label>
                            <input type="text" name="kode_produksi" class="form-control"
                            value="{{ old('kode_produksi',$iqf->kode_produksi) }}" required>

                            @error('kode_produksi')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- STD SUHU --}}
                        <div class="col-md-4">
                            <label class="form-label">Std CT (°C)</label>
                            <select name="std_suhu" class="form-control">
                                <option value="-18.0" {{ old('std_suhu',$iqf->std_suhu)=='-18.0'?'selected':'' }}>-18.0</option>
                                <option value="-10.0" {{ old('std_suhu',$iqf->std_suhu)=='-10.0'?'selected':'' }}>-10.0</option>
                            </select>

                            @error('std_suhu')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>


            {{-- SUHU --}}
            @php
            // AMANKAN DATA SUHU
            $safeSuhu = [];
            for($i=0;$i<10;$i++){
                $safeSuhu[$i]['value'] = $suhu_pusat[$i]['value'] ?? null;
                $safeSuhu[$i]['ket']   = $suhu_pusat[$i]['ket'] ?? null;
            }
            @endphp

            <div class="card mb-3">
                <div class="card-header bg-info text-white text-center">
                    <strong>Suhu Pusat Produk (°C)</strong>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Pukul</th>
                                <th></th>
                                @for($i = 0; $i < 10; $i++)
                                <th>{{ $i+1 }}</th>
                                @endfor
                                <th>X</th>
                            </tr>
                        </thead>

                        <tbody>

                            {{-- SUHU --}}
                            <tr>
                                <td rowspan="2">
                                    <input type="time"
                                    name="pukul"
                                    class="form-control form-control-sm"
                                    value="{{ old('pukul', $iqf->pukul) }}">

                                    @error('pukul')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>

                                <td>Suhu</td>

                                @for($i=0; $i<10; $i++)
                                <td>
                                    <input type="number"
                                    step="0.1"
                                    name="suhu_pusat[{{ $i }}][value]"
                                    class="form-control form-control-sm"
                                    value="{{ old("suhu_pusat.$i.value", $safeSuhu[$i]['value']) }}">
                                    @error("suhu_pusat.$i.value")
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>
                                @endfor

                                <td rowspan="2">
                                    <input type="number"
                                    step="0.01"
                                    name="average"
                                    class="form-control form-control-sm"
                                    value="{{ old('average', $iqf->average) }}">

                                    @error('average')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>
                            </tr>

                            {{-- KETERANGAN --}}
                            <tr>
                                <td>Keterangan</td>

                                @for($i=0; $i<10; $i++)
                                <td>
                                    <input type="text"
                                    name="suhu_pusat[{{ $i }}][ket]"
                                    class="form-control form-control-sm"
                                    value="{{ old("suhu_pusat.$i.ket", $safeSuhu[$i]['ket']) }}">

                                    @error("suhu_pusat.$i.ket")
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>
                                @endfor
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            {{-- NOTES --}}
            <div class="card mb-3">
                <div class="card-header bg-light"><strong>Problem</strong></div>
                <div class="card-body">
                    <textarea name="problem" class="form-control" rows="3">{{ old('problem',$iqf->problem) }}</textarea>

                    @error('problem')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="card-header bg-light"><strong>Tindakan Koreksi</strong></div>
                <div class="card-body">
                    <textarea name="tindakan_koreksi" class="form-control" rows="3">{{ old('tindakan_koreksi',$iqf->tindakan_koreksi) }}</textarea>

                    @error('tindakan_koreksi')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="card-header bg-light"><strong>Catatan</strong></div>
                <div class="card-body">
                    <textarea name="catatan" class="form-control" rows="3">{{ old('catatan',$iqf->catatan) }}</textarea>

                    @error('catatan')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>


            {{-- TOMBOL --}}
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-success w-auto">
                    <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('iqf.index') }}" class="btn btn-secondary w-auto">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

        </form>
    </div>
</div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

{{-- AUTO HITUNG AVERAGE --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let avgInput = document.querySelector('input[name="average"]');

        function calculateAverage() {
            let sum = 0, count = 0;

            for (let i = 0; i < 10; i++) {
                let input = document.querySelector(`input[name="suhu_pusat[${i}][value]"]`);
                let val = parseFloat(input.value);

                if (!isNaN(val)) {
                    sum += val;
                    count++;
                }
            }
            avgInput.value = count > 0 ? (sum / count).toFixed(2) : '';
        }

        for (let i = 0; i < 10; i++) {
            document
            .querySelector(`input[name="suhu_pusat[${i}][value]"]`)
            .addEventListener('input', calculateAverage);
        }
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

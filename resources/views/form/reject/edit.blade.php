@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Monitoring False Rejection</h4>

            <form method="POST" action="{{ route('reject.update', $reject->uuid) }}">
                @csrf
                @method('PUT')

                {{-- =================== Identitas Pemeriksaan =================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        {{-- Nama Mesin --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Mesin</label>
                                <input type="text" name="nama_mesin" class="form-control" 
                                value="{{ $reject->nama_mesin }}" readonly>
                            </div>
                        </div>

                        {{-- Tanggal & Shift --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" 
                                value="{{ $reject->date }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ $reject->shift == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ $reject->shift == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ $reject->shift == '3' ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        {{-- Nama Produk & Kode Produksi (langsung tampilkan) --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" name="nama_produk" class="form-control" 
                                value="{{ $reject->nama_produk }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" name="kode_produksi" class="form-control" 
                                value="{{ $reject->kode_produksi }}" readonly>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- =================== Monitoring False Rejection =================== --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Monitoring False Rejection</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Pack/Tray yang Tidak Lolos</label>
                                <input type="number" min="0" name="jumlah_tidak_lolos" class="form-control" 
                                value="{{ $reject->jumlah_tidak_lolos }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Pack/Tray yang Terdapat Kontaminan</label>
                                <input type="number" min="0" name="jumlah_kontaminan" class="form-control" 
                                value="{{ $reject->jumlah_kontaminan }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kontaminan</label>
                                <input type="text" name="jenis_kontaminan" class="form-control" 
                                value="{{ $reject->jenis_kontaminan }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Posisi Kontaminan</label>
                                <input type="text" name="posisi_kontaminan" class="form-control" 
                                value="{{ $reject->posisi_kontaminan }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">False Rejection</label>

                                <div class="input-group">
                                    <input type="text" 
                                    name="false_rejection" 
                                    class="form-control" 
                                    value="{{ $reject->false_rejection }}">

                                    <span class="input-group-text">/ <span id="jlolos_display">{{ $reject->jumlah_tidak_lolos }}</span></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3">{{ $reject->catatan }}</textarea>
                    </div>
                </div>

                {{-- Tombol aksi --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('reject.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const jml = document.querySelector("input[name='jumlah_tidak_lolos']");
        const display = document.getElementById("jlolos_display");

        jml.addEventListener("input", function () {
            display.textContent = jml.value ? jml.value : 0;
        });
    });
</script>

@endsection

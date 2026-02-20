@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Sekunder Produk Tidak Sesuai</h4>

            <form method="POST" action="{{ route('sekunder.update', $sekunder->uuid) }}">
                @csrf
                @method('PUT')

                {{-- ================= IDENTITAS ================= --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>

                    <div class="card-body">

                        {{-- Tanggal & Shift --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date"
                                id="dateInput"
                                name="date"
                                class="form-control"
                                value="{{ old('date', $sekunder->date) }}"
                                required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $sekunder->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $sekunder->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $sekunder->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>

                        {{-- Produk & Kode Produksi --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <select name="nama_produk"
                                class="form-control selectpicker"
                                data-live-search="true"
                                required>
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}"
                                    {{ old('nama_produk', $sekunder->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                    {{ $produk->nama_produk }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kode Produksi</label>
                            <input type="text"
                            name="kode_produksi"
                            class="form-control"
                            value="{{ old('kode_produksi', $sekunder->kode_produksi) }}"
                            required>
                        </div>
                    </div>

                    {{-- Best Before & Isi --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Best Before</label>
                            <input type="date"
                            id="bestBeforeInput"
                            name="best_before"
                            class="form-control"
                            value="{{ old('best_before', $sekunder->best_before) }}"
                            required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Isi Per Zak (bag)</label>
                            <input type="number"
                            name="isi_per_zak"
                            class="form-control"
                            value="{{ old('isi_per_zak', $sekunder->isi_per_zak) }}"
                            required>
                        </div>
                    </div>

                    {{-- Jumlah & Petugas --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Produk Dikemas (zak)</label>
                            <input type="number"
                            name="jumlah_produk"
                            class="form-control"
                            value="{{ old('jumlah_produk', $sekunder->jumlah_produk) }}"
                            required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Petugas</label>
                            <input type="text"
                            name="petugas"
                            class="form-control"
                            value="{{ old('petugas', $sekunder->petugas) }}"
                            required>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ================= CATATAN ================= --}}
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong>Catatan</strong>
                </div>
                <div class="card-body">
                    <textarea name="catatan"
                    class="form-control"
                    rows="3">{{ old('catatan', $sekunder->catatan) }}</textarea>
                </div>
            </div>

            {{-- ================= BUTTON ================= --}}
            <div class="d-flex justify-content-between">
                <button class="btn btn-success">
                    <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('sekunder.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

        </form>
    </div>
</div>
</div>

{{-- ================= SCRIPT ================= --}}
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const bestBeforeInput = document.getElementById("bestBeforeInput");

        function hitungBestBefore() {
            if (!dateInput.value) return;

            let date = new Date(dateInput.value);
            date.setMonth(date.getMonth() + 4);

            let y = date.getFullYear();
            let m = String(date.getMonth() + 1).padStart(2, '0');
            let d = String(date.getDate()).padStart(2, '0');

            bestBeforeInput.value = `${y}-${m}-${d}`;
        }

    // 🔑 KHUSUS EDIT:
    // Best before hanya berubah kalau tanggal diganti
        dateInput.addEventListener("change", hitungBestBefore);
    });
</script>

@endsection

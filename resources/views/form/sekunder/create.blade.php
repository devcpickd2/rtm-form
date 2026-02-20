@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Pemeriksaan sekunder Produk tidak Sesuai</h4>
            <form method="POST" action="{{ route('sekunder.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
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
                            <label class="form-label">Nama Produk</label>
                            <select id="nama_produk" name="nama_produk" class="form-control selectpicker" data-live-search="true" title="Ketik nama produk..." required>
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
                        <label class="form-label">Best Before</label>
                        <input type="date" id="bestBeforeInput" name="best_before" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Isi Per Zak (bag)</label>
                        <input type="number" id="isi_per_zak" name="isi_per_zak" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jumlah Produk dikemas (zak)</label>
                        <input type="number" id="jumlah_produk" name="jumlah_produk" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Petugas</label>
                        <input type="text" id="petugas" name="petugas" class="form-control" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="card mb-3">
            <div class="card-header bg-light">
                <div class="card-header bg-light">
                    <strong>Catatan</strong>
                </div>
                <div class="card-body">
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada"></textarea>
                </div>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success w-auto">
                <i class="bi bi-save"></i> Simpan
            </button>
            <a href="{{ route('sekunder.index') }}" class="btn btn-secondary w-auto">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

</form>
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const shiftInput = document.getElementById("shiftInput");
        const bestBeforeInput = document.getElementById("bestBeforeInput");

    // Ambil waktu sekarang
        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = now.getHours();

    // Set tanggal hari ini
        dateInput.value = `${yyyy}-${mm}-${dd}`;

    // Tentukan shift
        if (hh >= 7 && hh < 15) shiftInput.value = "1";
        else if (hh >= 15 && hh < 23) shiftInput.value = "2";
        else shiftInput.value = "3";

    // === HITUNG BEST BEFORE +4 BULAN ===
        function setBestBefore() {
            if (!dateInput.value) return;

            let date = new Date(dateInput.value);
            date.setMonth(date.getMonth() + 4);

            let y = date.getFullYear();
            let m = String(date.getMonth() + 1).padStart(2, '0');
            let d = String(date.getDate()).padStart(2, '0');

            bestBeforeInput.value = `${y}-${m}-${d}`;
        }

    // Set saat load
        setBestBefore();

    // Update jika tanggal diubah
        dateInput.addEventListener("change", setBestBefore);
    });
</script>

@endsection

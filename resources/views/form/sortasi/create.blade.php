@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Sortasi Bahan Baku yang Tidak Sesuai</h4>
            <form method="POST" action="{{ route('sortasi.store') }}" enctype="multipart/form-data">
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
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Sortasi Bahan Baku</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Bahan</label>
                                <input type="text" id="nama_bahan" name="nama_bahan" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Jumlah Bahan Sebelum Sortasi</label>
                                <input type="number" id="jumlah_bahan" name="jumlah_bahan" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jumlah Bahan Sesuai</label>
                                <input type="number" id="jumlah_sesuai" name="jumlah_sesuai" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jumlah Bahan Tidak Sesuai</label>
                                <input type="number" id="jumlah_tidak_sesuai" name="jumlah_tidak_sesuai" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Tindakan Koreksi</label>
                                <textarea name="tindakan_koreksi" class="form-control" rows="3" placeholder="Tulis tindakan koreksi"></textarea>
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
                    <a href="{{ route('sortasi.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const timeInput = document.getElementById("timeInput");
        const shiftInput = document.getElementById("shiftInput");

    // Ambil waktu sekarang
        let now = new Date();
        let yyyy = now.getFullYear();
        let mm = String(now.getMonth() + 1).padStart(2, '0');
        let dd = String(now.getDate()).padStart(2, '0');
        let hh = String(now.getHours()).padStart(2, '0');
        let min = String(now.getMinutes()).padStart(2, '0');

    // Set value tanggal dan jam
        dateInput.value = `${yyyy}-${mm}-${dd}`;
        timeInput.value = `${hh}:${min}`;

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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const hasilInput = document.getElementById("hasil_tera");
        const hasilWarning = document.getElementById("hasilWarning");

        hasilInput.addEventListener("input", function () {
            let value = parseFloat(hasilInput.value);
            if (!isNaN(value) && Math.abs(value) > 0.4) {
                hasilWarning.classList.remove("d-none");
            } else {
                hasilWarning.classList.add("d-none");
            }
        });
    });
</script>
@endsection

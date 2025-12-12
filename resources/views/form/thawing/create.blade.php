@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Pemeriksaan Proses Thawing</h4>
            <form method="POST" action="{{ route('thawing.store') }}" enctype="multipart/form-data">
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
                    <div class="card-header bg-info text-white">
                        <strong>Pemeriksaan Produk Thawing</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kondisi Ruangan</label>
                                <input type="text" id="kondisi_ruangan" name="kondisi_ruangan" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Produk</label>
                                <input type="text" id="jenis_produk" name="jenis_produk" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan Sebelum Thawing --}}
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <strong>Sebelum Proses Thawing</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah</label>
                                <input type="number" id="jumlah" name="jumlah" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kondisi Produk</label>
                                <select id="kondisi_produk" name="kondisi_produk" class="form-control" required>
                                    <option value="">-- Pilih Kondisi --</option>
                                    <option value="Oke" {{ old('kondisi_produk') == 'Oke' ? 'selected' : '' }}>Oke</option>
                                    <option value="Tidak Oke" {{ old('kondisi_produk') == 'Tidak Oke' ? 'selected' : '' }}>Tidak Oke</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <input type="text" id="keterangan_kondisi" name="keterangan_kondisi" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Suhu Ruangan °C</label>
                                <input type="number" id="suhu_ruangan" name="suhu_ruangan" class="form-control" step="any">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mulai Thawing Pukul</label>
                                <input type="time" id="mulai_thawing" name="mulai_thawing" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan Setelah Thawing --}}
                <div class="card mb-3">
                    <div class="card-header bg-danger text-white">
                        <strong>Setelah Proses Thawing</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Selesai Thawing Pukul</label>
                                <input type="time" id="selesai_thawing" name="selesai_thawing" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kondisi Produk</label>
                                <select id="kondisi_produk_setelah" name="kondisi_produk_setelah" class="form-control" required>
                                    <option value="">-- Pilih Kondisi --</option>
                                    <option value="Oke" {{ old('kondisi_produk_setelah') == 'Oke' ? 'selected' : '' }}>Oke</option>
                                    <option value="Tidak Oke" {{ old('kondisi_produk_setelah') == 'Tidak Oke' ? 'selected' : '' }}>Tidak Oke</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <input type="text" id="keterangan_kondisi_setelah" name="keterangan_kondisi_setelah" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah</label>
                                <input type="number" id="jumlah_setelah" name="jumlah_setelah" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Suhu Produk (5-10°C)</label>
                                <input type="number" id="suhu_produk" name="suhu_produk" class="form-control" step="any">
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
                    <a href="{{ route('thawing.index') }}" class="btn btn-secondary w-auto">
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
@endsection

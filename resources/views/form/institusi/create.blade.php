@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-plus-circle"></i> Form Input Verifikasi Produk Institusi</h4>
            <form method="POST" action="{{ route('institusi.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Bagian Identitas --}}
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Identitas Pemeriksaan</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-4">
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
                        <strong>Pemeriksaan Institusi</strong>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Produk</label>
                                <select id="jenis_produk" name="jenis_produk" class="form-control" required>
                                    <option value="">-- Pilih Jenis Institusi --</option>
                                    @foreach($listInstitusi as $item)
                                    <option value="{{ $item->nama_institusi }}">{{ $item->nama_institusi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Waktu Proses</label>
                                <div class="input-group">
                                    <input type="time" id="waktu_proses_mulai" name="waktu_proses_mulai" class="form-control" required>
                                    <span class="input-group-text">s/d</span>
                                    <input type="time" id="waktu_proses_selesai" name="waktu_proses_selesai" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lokasi</label>
                                <input type="text" id="lokasi" name="lokasi" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Pemeriksaan Suhu Produk</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Sebelum (°C)</label>
                                <input type="text" id="suhu_sebelum" name="suhu_sebelum" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sesudah (°C)</label>
                                <input type="text" id="suhu_sesudah" name="suhu_sesudah" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Sensori</strong>
                    </div>

                    {{-- Notes --}}
                    <div class="alert alert-warning mt-2 py-2 px-3" style="font-size: 0.9rem;">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong>  
                        <ul class="mb-0 ps-3">
                            <li>Sensori rasa dan tekstur untuk produk yang melewati proses steam</li>
                            <li>Sensori aroma, warna, dan penampakan hanya untuk produk hasil proses thawing</li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label class="form-label">Sensori</label>
                            <select id="sensori" name="sensori" class="form-control">
                                <option value="Oke">Oke</option>
                                <option value="Tidak Oke">Tidak Oke</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Keterangan</label>
                            <input type="text" id="keterangan" name="keterangan" class="form-control">
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
                <a href="{{ route('institusi.index') }}" class="btn btn-secondary w-auto">
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

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Pemeriksaan Proses Thawing</h4>
            <form method="POST" action="{{ route('thawing.update', $thawing->uuid) }}" enctype="multipart/form-data">
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
                                <label class="form-label">Tanggal</label>
                                <input type="date" id="dateInput" name="date" 
                                    class="form-control" 
                                    value="{{ old('date', $thawing->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $thawing->shift) == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $thawing->shift) == '2' ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $thawing->shift) == '3' ? 'selected' : '' }}>Shift 3</option>
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
                                <input type="text" id="kondisi_ruangan" name="kondisi_ruangan" 
                                    class="form-control" 
                                    value="{{ old('kondisi_ruangan', $thawing->kondisi_ruangan) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Produk</label>
                                <select name="jenis_produk" id="jenis_produk"
                                class="form-control @error('jenis_produk') is-invalid @enderror" required>

                                <option value="">-- Pilih Jenis Produk --</option>

                                @foreach($dagings as $daging)
                                <option value="{{ $daging->nama_daging }}"
                                    {{ old('jenis_produk', $thawing->jenis_produk) == $daging->nama_daging ? 'selected' : '' }}>
                                    {{ $daging->nama_daging }}
                                </option>
                                @endforeach

                            </select>

                            @error('jenis_produk')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" 
                                    class="form-control" 
                                    value="{{ old('kode_produksi', $thawing->kode_produksi) }}" required>
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
                                <input type="number" id="jumlah" name="jumlah" class="form-control"
                                    value="{{ old('jumlah', $thawing->jumlah) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kondisi Produk</label>
                                <select id="kondisi_produk" name="kondisi_produk" class="form-control" required>
                                    <option value="">-- Pilih Kondisi --</option>
                                    <option value="Oke" {{ old('kondisi_produk', $thawing->kondisi_produk) == 'Oke' ? 'selected' : '' }}>Oke</option>
                                    <option value="Tidak Oke" {{ old('kondisi_produk', $thawing->kondisi_produk) == 'Tidak Oke' ? 'selected' : '' }}>Tidak Oke</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <input type="text" id="keterangan_kondisi" name="keterangan_kondisi" class="form-control"
                                    value="{{ old('keterangan_kondisi', $thawing->keterangan_kondisi) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Suhu Ruangan °C</label>
                                <input type="number" id="suhu_ruangan" name="suhu_ruangan" class="form-control" step="any"
                                    value="{{ old('suhu_ruangan', $thawing->suhu_ruangan) }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mulai Thawing Pukul</label>
                                <input type="time" id="mulai_thawing" name="mulai_thawing" class="form-control"
                                    value="{{ old('mulai_thawing', $thawing->mulai_thawing) }}">
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
                                <input type="time" id="selesai_thawing" name="selesai_thawing" class="form-control"
                                    value="{{ old('selesai_thawing', $thawing->selesai_thawing) }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kondisi Produk</label>
                                <select id="kondisi_produk_setelah" name="kondisi_produk_setelah" class="form-control" required>
                                    <option value="">-- Pilih Kondisi --</option>
                                    <option value="Oke" {{ old('kondisi_produk_setelah', $thawing->kondisi_produk_setelah) == 'Oke' ? 'selected' : '' }}>Oke</option>
                                    <option value="Tidak Oke" {{ old('kondisi_produk_setelah', $thawing->kondisi_produk_setelah) == 'Tidak Oke' ? 'selected' : '' }}>Tidak Oke</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <input type="text" id="keterangan_kondisi_setelah" name="keterangan_kondisi_setelah" class="form-control"
                                    value="{{ old('keterangan_kondisi_setelah', $thawing->keterangan_kondisi_setelah) }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jumlah</label>
                                <input type="number" id="jumlah_setelah" name="jumlah_setelah" class="form-control"
                                    value="{{ old('jumlah_setelah', $thawing->jumlah_setelah) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Suhu Produk (5-10°C)</label>
                                <input type="number" id="suhu_produk" name="suhu_produk" class="form-control" step="any"
                                    value="{{ old('suhu_produk', $thawing->suhu_produk) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Catatan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $thawing->catatan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-primary w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('thawing.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4>
                <i class="bi bi-pencil-square"></i> Edit Verifikasi Magnet Trap
            </h4>

            <form method="POST"
            action="{{ route('verifikasi_magnet.update', $verifikasi_magnet->uuid) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Bagian Identitas --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong>Identitas Pemeriksaan</strong>
                </div>

                <div class="card-body">
                    <div class="row mb-3">

                        <div class="col-md-4">
                            <label class="form-label">Tanggal</label>
                            <input
                            type="date"
                            id="dateInput"
                            name="date"
                            class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', $verifikasi_magnet->date ? \Carbon\Carbon::parse($verifikasi_magnet->date)->format('Y-m-d') : '') }}"
                            required
                            >

                            @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Shift</label>
                            <select
                            id="shiftInput"
                            name="shift"
                            class="form-control @error('shift') is-invalid @enderror"
                            required
                            >
                            <option value="1" {{ old('shift', $verifikasi_magnet->shift) == 1 ? 'selected' : '' }}>
                                Shift 1
                            </option>

                            <option value="2" {{ old('shift', $verifikasi_magnet->shift) == 2 ? 'selected' : '' }}>
                                Shift 2
                            </option>

                            <option value="3" {{ old('shift', $verifikasi_magnet->shift) == 3 ? 'selected' : '' }}>
                                Shift 3
                            </option>
                        </select>

                        @error('shift')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pukul</label>
                        <input
                        type="time"
                        id="timeInput"
                        name="pukul"
                        class="form-control @error('pukul') is-invalid @enderror"
                        value="{{ old('pukul', $verifikasi_magnet->pukul ? \Carbon\Carbon::parse($verifikasi_magnet->pukul)->format('H:i') : '') }}"
                        required
                        >

                        @error('pukul')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- Bagian Pemeriksaan --}}
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <strong>Magnet Trap</strong>
            </div>

            <div class="card-body">

                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">Nama Produk</label>

                        <input
                        type="text"
                        id="nama_produk"
                        name="nama_produk"
                        class="form-control @error('nama_produk') is-invalid @enderror"
                        value="{{ old('nama_produk', $verifikasi_magnet->nama_produk) }}"
                        required
                        >

                        @error('nama_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kode Produksi</label>

                        <input
                        type="text"
                        id="kode_produksi"
                        name="kode_produksi"
                        class="form-control @error('kode_produksi') is-invalid @enderror"
                        value="{{ old('kode_produksi', $verifikasi_magnet->kode_produksi) }}"
                        required
                        >

                        @error('kode_produksi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="row mb-3">

                    <div class="col-md-6">
                        <label class="form-label">Jumlah Temuan</label>

                        <input
                        type="number"
                        id="jumlah_temuan"
                        name="jumlah_temuan"
                        class="form-control @error('jumlah_temuan') is-invalid @enderror"
                        value="{{ old('jumlah_temuan', $verifikasi_magnet->jumlah_temuan) }}"
                        required
                        >

                        @error('jumlah_temuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Bukti (upload gambar)</label>

                        <input
                        type="file"
                        name="bukti"
                        class="form-control @error('bukti') is-invalid @enderror"
                        accept="image/*"
                        >

                        @error('bukti')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Bukti lama --}}
                        @if($verifikasi_magnet->bukti)
                        <div class="mt-2">
                            <small class="text-muted d-block mb-1">
                                Bukti saat ini:
                            </small>

                            <a href="{{ asset('storage/' . $verifikasi_magnet->bukti) }}"
                               target="_blank">
                               <img
                               src="{{ asset('storage/' . $verifikasi_magnet->bukti) }}"
                               alt="Bukti"
                               style="max-width: 150px; height: auto; cursor: pointer;"
                               class="img-thumbnail"
                               >
                           </a>
                       </div>
                       @endif

                   </div>

               </div>

           </div>
       </div>

       {{-- Catatan --}}
       <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Keterangan</strong>
        </div>

        <div class="card-body">
            <textarea
            name="keterangan"
            class="form-control"
            rows="3"
            placeholder="Tambahkan keterangan bila ada"
            >{{ old('keterangan', $verifikasi_magnet->keterangan) }}</textarea>
        </div>
    </div>

    {{-- Tombol --}}
    <div class="d-flex justify-content-between mt-3">

        <button type="submit" class="btn btn-success w-auto">
            <i class="bi bi-save"></i> Update
        </button>

        <a
        href="{{ route('verifikasi_magnet.index') }}"
        class="btn btn-secondary w-auto"
        >
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

</div>

</form>
</div>
</div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>

<link rel="stylesheet"
href="{{ asset('assets/css/bootstrap-select.min.css') }}">

<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>

@endsection
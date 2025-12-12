@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body"> 
            <h4><i class="bi bi-pencil-square"></i> Edit Kontaminasi Benda Asing</h4>
            <form method="POST" action="{{ route('kontaminasi.update', $kontaminasi->uuid) }}" enctype="multipart/form-data">
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
                                <input type="date" id="dateInput" name="date" class="form-control" 
                                value="{{ old('date', $kontaminasi->date) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <select id="shiftInput" name="shift" class="form-control" required>
                                    <option value="1" {{ old('shift', $kontaminasi->shift) == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $kontaminasi->shift) == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $kontaminasi->shift) == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pukul</label>
                                <input type="time" id="timeInput" name="pukul" class="form-control" 
                                value="{{ old('pukul', $kontaminasi->pukul) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Pemeriksaan --}}
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <strong>Kontaminasi</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kontaminasi</label>
                                <input type="text" id="jenis_kontaminasi" name="jenis_kontaminasi" 
                                class="form-control" 
                                value="{{ old('jenis_kontaminasi', $kontaminasi->jenis_kontaminasi) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bukti Kontaminasi (upload gambar)</label>
                                @if($kontaminasi->bukti)

                                <input type="file" 
                                name="bukti" 
                                class="form-control @error('bukti') is-invalid @enderror"
                                accept="image/*" id="bukti">
                                <div class="mb-2">
                                    <a href="{{ asset('storage/'.$kontaminasi->bukti) }}" target="_blank">
                                        Lihat Gambar Sebelumnya
                                    </a>
                                </div>
                                @endif

                                @error('bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                         <div class="col-md-6">
                            <label class="form-label">Nama Produk</label>
                            <input list="produkList" id="nama_produk" name="nama_produk" 
                            class="form-control" placeholder="Ketik atau pilih produk..." required
                            value="{{ old('nama_produk', $kontaminasi->nama_produk) }}">
                            <datalist id="produkList">
                                @foreach($produks as $produk)
                                <option value="{{ $produk->nama_produk }}">
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Produksi</label>
                                <input type="text" id="kode_produksi" name="kode_produksi" class="form-control" 
                                value="{{ old('kode_produksi', $kontaminasi->kode_produksi) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Tahapan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="tahapan" class="form-control" placeholder="Tuliskan Tahapan">{{ old('tahapan', $kontaminasi->tahapan) }}</textarea>
                    </div>
                    <div class="card-header bg-light">
                        <strong>Tindakan Koreksi</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="tindakan_koreksi" class="form-control" placeholder="Tuliskan tindakan koreksi">{{ old('tindakan_koreksi', $kontaminasi->tindakan_koreksi) }}</textarea>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Catatan</strong>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan bila ada">{{ old('catatan', $kontaminasi->catatan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('kontaminasi.index') }}" class="btn btn-secondary w-auto">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#nama_produk').select2({
            tags: true,
            placeholder: "Ketik atau pilih nama produk...",
            allowClear: true,
            width: '100%',
            data: [
                @foreach($produks as $produk)
                {id: '{{ $produk->nama_produk }}', text: '{{ $produk->nama_produk }}'},
                @endforeach
            ]
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("dateInput");
        const timeInput = document.getElementById("timeInput");
        const shiftInput = document.getElementById("shiftInput");

        if (!dateInput.value) {
            let now = new Date();
            let yyyy = now.getFullYear();
            let mm = String(now.getMonth() + 1).padStart(2, '0');
            let dd = String(now.getDate()).padStart(2, '0');
            let hh = String(now.getHours()).padStart(2, '0');
            let min = String(now.getMinutes()).padStart(2, '0');

            dateInput.value = `${yyyy}-${mm}-${dd}`;
            timeInput.value = `${hh}:${min}`;

            let hour = parseInt(hh);
            if (hour >= 7 && hour < 15) {
                shiftInput.value = "1";
            } else if (hour >= 15 && hour < 23) {
                shiftInput.value = "2";
            } else {
                shiftInput.value = "3"; 
            }
        }
    });
</script>
@endsection

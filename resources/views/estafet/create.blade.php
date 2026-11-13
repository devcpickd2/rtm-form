@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">➕ Tambah Produk</h3>

            {{-- Alert error jika validasi gagal --}}
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ups!</strong> Ada kesalahan pada inputan Anda:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Form Input --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('estafet.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nama_alat" class="form-label">Nama Alat</label>
                            <input
                            type="text"
                            name="nama_alat"
                            class="form-control @error('nama_alat') is-invalid @enderror"
                            placeholder="Masukkan Nama Alat"
                            value="{{ old('nama_alat') }}">
                            @error('nama_alat')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror 
                        </div>

                        <div class="mb-3">
                            <label for="area" class="form-label">Area</label>
                            <select
                            name="area"
                            class="form-control @error('area') is-invalid @enderror"
                            >
                            <option value="">— Pilih Area —</option>
                            <option value="1" {{ old('area') == '1' ? 'selected' : '' }}>Cooking</option>
                            <option value="2" {{ old('area') == '2' ? 'selected' : '' }}>Packing</option>
                            <option value="3" {{ old('area') == '3' ? 'selected' : '' }}>Warehouse</option>
                        </select>

                        @error('area')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input
                        type="number"
                        name="jumlah"
                        class="form-control @error('jumlah') is-invalid @enderror"
                        placeholder="Masukkan Nama Alat"
                        value="{{ old('jumlah') }}">
                        @error('jumlah')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror 
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">💾 Simpan</button>
                        <a href="{{ route('estafet.index') }}" class="btn btn-secondary">⬅ Kembali</a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
</div>
@endsection
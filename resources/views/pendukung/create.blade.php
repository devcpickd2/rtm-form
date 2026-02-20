@extends('layouts.app') {{-- Layout utama --}}

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">➕ Tambah Nama Karyawan</h3>

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
                    <form action="{{ route('pendukung.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nama_karyawan" class="form-label">Nama Karyawan</label>
                            <input
                            type="text"
                            name="nama_karyawan"
                            class="form-control @error('nama_karyawan') is-invalid @enderror"
                            placeholder="Masukkan Karyawan Baru.."
                            value="{{ old('nama_karyawan') }}">
                            @error('nama_karyawan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="area" class="form-label">Area</label>
                            <select
                            name="area"
                            id="area"
                            class="form-control @error('area') is-invalid @enderror">
                            <option value="">-- Pilih Area --</option>
                            <option value="Engineer" {{ old('area') == 'Engineer' ? 'selected' : '' }}>Engineer</option>
                            <option value="Warehouse" {{ old('area') == 'Warehouse' ? 'selected' : '' }}>Warehouse</option>
                            <option value="Operator" {{ old('area') == 'Operator' ? 'selected' : '' }}>Operator</option>
                        </select>
                        @error('area')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">💾 Simpan</button>
                        <a href="{{ route('pendukung.index') }}" class="btn btn-secondary">⬅ Kembali</a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
</div>
@endsection
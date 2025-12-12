@extends('layouts.app') {{-- Layout utama --}}

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
                    <form action="{{ route('listpremix.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nama_premix" class="form-label">Nama Premix</label>
                            <input
                            type="text"
                            name="nama_premix"
                            class="form-control @error('nama_premix') is-invalid @enderror"
                            placeholder="Masukkan Nama Premix   "
                            value="{{ old('nama_premix') }}">
                            @error('nama_premix')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror 
                        </div>

                        <div class="mb-3">
                            <label for="alergen" class="form-label">Alergen / Non Alergen</label>
                            <select
                            name="alergen"
                            class="form-control @error('alergen') is-invalid @enderror"
                            >
                            <option value="">— Pilih Status —</option>
                            <option value="Alergen" {{ old('alergen') == 'Alergen' ? 'selected' : '' }}>Alergen</option>
                            <option value="Non Alergen" {{ old('alergen') == 'Non Alergen' ? 'selected' : '' }}>Non Alergen</option>
                        </select>

                        @error('alergen')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">💾 Simpan</button>
                        <a href="{{ route('listpremix.index') }}" class="btn btn-secondary">⬅ Kembali</a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
</div>
@endsection
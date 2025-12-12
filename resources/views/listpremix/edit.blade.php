@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">✏ Edit List Premix</h3>

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

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('listpremix.update', $listpremix->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nama Premix --}}
                        <div class="mb-3">
                            <label for="nama_premix" class="form-label">Nama Premix</label>
                            <input type="text"
                                name="nama_premix"
                                class="form-control @error('nama_premix') is-invalid @enderror"
                                value="{{ old('nama_premix', $listpremix->nama_premix) }}"
                                placeholder="Masukkan Nama Premix">
                            @error('nama_premix')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alergen --}}
                        <div class="mb-3">
                            <label for="alergen" class="form-label">Alergen / Non Alergen</label>
                            <select name="alergen"
                                class="form-control @error('alergen') is-invalid @enderror">

                                <option value="">— Pilih Status —</option>
                                <option value="Alergen" 
                                    {{ old('alergen', $listpremix->alergen) == 'Alergen' ? 'selected' : '' }}>
                                    Alergen
                                </option>
                                <option value="Non Alergen" 
                                    {{ old('alergen', $listpremix->alergen) == 'Non Alergen' ? 'selected' : '' }}>
                                    Non Alergen
                                </option>
                            </select>
                            @error('alergen')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">💾 Update</button>
                            <a href="{{ route('listpremix.index') }}" class="btn btn-secondary">⬅ Kembali</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

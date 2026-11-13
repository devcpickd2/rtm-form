@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">✏️ Edit Alat</h3>

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

                    <form action="{{ route('estafet.update', $estafet->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nama Alat</label>
                            <input
                                type="text"
                                name="nama_alat"
                                class="form-control @error('nama_alat') is-invalid @enderror"
                                value="{{ old('nama_alat', $estafet->nama_alat) }}"
                                placeholder="Masukkan Nama Alat">

                            @error('nama_alat')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Area</label>
                            <select
                                name="area"
                                class="form-control @error('area') is-invalid @enderror">

                                <option value="">-- Pilih Area --</option>
                                <option value="1"
                                    {{ old('area', $estafet->area) == '1' ? 'selected' : '' }}>
                                    Cooking
                                </option>

                                <option value="2"
                                    {{ old('area', $estafet->area) == '2' ? 'selected' : '' }}>
                                    Packing
                                </option>

                                <option value="3"
                                    {{ old('area', $estafet->area) == '3' ? 'selected' : '' }}>
                                    Warehouse
                                </option>

                            </select>

                            @error('area')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input
                                type="number"
                                min="0"
                                name="jumlah"
                                class="form-control @error('jumlah') is-invalid @enderror"
                                value="{{ old('jumlah', $estafet->jumlah) }}"
                                placeholder="Masukkan Jumlah">

                            @error('jumlah')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-warning">
                                💾 Update
                            </button>

                            <a href="{{ route('estafet.index') }}"
                               class="btn btn-secondary">
                                ⬅ Kembali
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
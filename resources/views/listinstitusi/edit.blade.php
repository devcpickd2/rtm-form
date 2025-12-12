@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h3 class="mb-4">✏️ Edit Institusi</h3>

            {{-- Alert error --}}
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
                    <form action="{{ route('listinstitusi.update', $listinstitusi->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nama_institusi" class="form-label">Nama Institusi</label>
                            <input
                                type="text"
                                name="nama_institusi"
                                class="form-control @error('nama_institusi') is-invalid @enderror"
                                placeholder="Masukkan Nama Institusi"
                                value="{{ old('nama_institusi', $listinstitusi->nama_institusi) }}"
                            >
                            @error('nama_institusi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-warning">🔄 Update</button>
                            <a href="{{ route('listinstitusi.index') }}" class="btn btn-secondary">⬅ Kembali</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
           <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>📋 Data Departemen</h3>
            <a href="{{ route('departemen.create') }}" class="btn btn-success">+ Tambah</a>
        </div>
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-primary">
                <tr>
                    <th scope="col" style="width: 25%;">Tanggal Dibuat</th>
                    <th scope="col">Nama Departemen</th>
                    <th scope="col" style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($departemens as $dep)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($dep->created_at)->format('d-m-Y H:i') }}</td>
                    <td>{{ $dep->nama }}</td>
                    <td class="text-center">
                        <a href="{{ route('departemen.edit', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('departemen.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Belum ada data departemen.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
</div>
<style>
    .pagination {
        justify-content: end;
    }
    .pagination .page-link {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }
    /* Header tabel merah */
    .table thead {
        background-color: #dc3545 !important; /* merah gelap */
        color: #fff;
    }

/* Baris tabel stripe merah muda */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f8d7da; /* merah muda terang */
}

.table-striped tbody tr:nth-of-type(even) {
    background-color: #f5c2c7; /* merah muda agak gelap */
}

/* Hover baris merah gelap */
.table tbody tr:hover {
    background-color: #e4606d !important;
    color: #fff;
}

/* Border tabel merah */
.table-bordered th, .table-bordered td {
    border-color: #dc3545;
}

/* Tombol aksi tetap jelas */
.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #b02a37;
    border-color: #a52834;
}
</style>
@endsection
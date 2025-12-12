@extends('layouts.app')

@section('content')
<div class="container py-4">

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-trash"></i> Recycle Bin Premix</h3>
            <a href="{{ route('listpremix.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <table class="table table-striped table-bordered align-middle">
            <thead class="table-danger text-center">
                <tr>
                    <th>Nama Premix</th>
                    <th>Alergen</th>
                    <th>Dihapus Pada</th>
                    <th width="25%">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($listpremix as $item)
                <tr>
                    <td>{{ $item->nama_premix }}</td>
                    <td>{{ $item->alergen }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>
                    <td class="text-center">

                        {{-- Restore --}}
                        <form action="{{ route('listpremix.restore', $item->uuid) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">
                                <i class="bi bi-arrow-clockwise"></i> Restore
                            </button>
                        </form>

                        {{-- Delete Permanent --}}
                        <form action="{{ route('listpremix.deletePermanent', $item->uuid) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Hapus permanen?')">
                                <i class="bi bi-x-circle"></i> Delete
                            </button>
                        </form>

                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="4" class="text-center">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            {{ $listpremix->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

</div>
@endsection

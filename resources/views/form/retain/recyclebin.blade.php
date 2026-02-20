@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-trash"></i> Recycle Bin Retained Sample</h3>
                <a href="{{ route('retain.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Plant</th>
                            <th>Sample Type</th>
                            <th>Collection Date</th>
                            <th>Sample Storage</th>
                            <th>Description</th>
                            <th>Production Code</th>
                            <th>Best Before</th>
                            <th>Qauntity (gr)</th>
                            <th>Remarks</th>
                            <th>QC</th>
                            <th>Deleted at</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($retain as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ $item->plant }}</td>
                            <td class="text-center align-middle">{{ $item->sample_type }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                            <td class="text-center align-middle">
                                @php
                                // decode json sample_storage jadi array
                                $namaStorage = is_array($item->sample_storage)
                                ? $item->sample_storage
                                : json_decode($item->sample_storage, true);
                                if (!$namaStorage) $namaStorage = [];
                                @endphp

                                {{-- tampilkan sebagai list koma --}}
                                {{ implode(', ', $namaStorage) }}
                            </td>
                            <td class="text-center align-middle">{{ $item->description }}</td>
                            <td class="text-center align-middle">{{ $item->production_code }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->best_before)->format('d-m-Y') }}</td>
                            <td class="text-center align-middle">{{ $item->quantity ?: '-' }}</td>
                            <td class="text-center align-middle">{{ $item->remarks ?: '-' }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('retain.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('retain.deletePermanent', $item->uuid) }}" 
                                  method="POST" class="d-inline">
                                  @csrf
                                  @method('DELETE')
                                  <button class="btn btn-danger btn-sm mb-1"
                                  onclick="return confirm('Hapus permanen?')">
                                  <i class="bi bi-x-circle"></i> Delete
                              </button>
                          </form>
                      </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="13" class="text-center align-middle">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $retain->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection

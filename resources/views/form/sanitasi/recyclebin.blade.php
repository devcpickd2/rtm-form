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
                <h3><i class="bi bi-trash"></i> Recycle Bin Pemeriksaan Sanitasi</h3>
                <a href="{{ route('sanitasi.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Pukul</th>
                            <th>Standar Footbasin</th>
                            <th>Aktual Footbasin</th>
                            <th>Standar Handbasin</th>
                            <th>Aktual Handbasin</th>
                            <th>Keterangan</th>
                            <th>Tindakan Koreksi</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($sanitasi as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                            <td class="text-center align-middle">{{ $item->std_footbasin }}</td>
                            <td class="text-center align-middle">
                                @if($item->aktual_footbasin)
                                <a href="{{ asset('storage/' . $item->aktual_footbasin) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $item->aktual_footbasin) }}" alt="Foot Basin"
                                    style="max-width:100px; height:auto; cursor:pointer;">
                                </a>
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-center align-middle">{{ $item->std_handbasin }}</td>
                            <td class="text-center align-middle">
                                @if($item->aktual_handbasin)
                                <a href="{{ asset('storage/' . $item->aktual_handbasin) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $item->aktual_handbasin) }}" alt="Hand Basin"
                                    style="max-width:100px; height:auto; cursor:pointer;">
                                </a>
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-center align-middle">{{ $item->keterangan }}</td>
                            <td class="text-center align-middle">{{ $item->tindakan_koreksi }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>
                            <td class="text-center align-middle">
                                <form action="{{ route('sanitasi.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('sanitasi.deletePermanent', $item->uuid) }}" 
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
                    <td colspan="11" class="text-center align-middle">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $sanitasi->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection

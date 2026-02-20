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
                <h3><i class="bi bi-trash"></i> Recycle Bin Kebersihan Ruang</h3>
                <a href="{{ route('kebersihan_ruang.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Rice - Boiling</th>
                            <th>Noodle</th>
                            <th>Chillroom</th>
                            <th>CS 1</th>
                            <th>CS 2</th>
                            <th>Seasoning</th>
                            <th>Prep Room</th>
                            <th>Cooking</th>
                            <th>Filling</th>
                            <th>Topping</th>
                            <th>Packing</th>
                            <th>IQF</th>
                            <th>CS FG</th>
                            <th>DryStore</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                        function checkIcon($arr) {
                            return !empty($arr['jam'])
                            ? '<i class="bi bi-check-circle-fill text-success"></i>'
                            : '<i class="bi bi-x-circle text-danger"></i>';
                        }
                        @endphp

                        @forelse ($kebersihan_ruang as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->rice_boiling ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->noodle ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->cr_rm ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->cs_1 ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->cs_2 ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->seasoning ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->prep_room ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->cooking ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->filling ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->topping ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->packing ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->iqf ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->cs_fg ?? []) !!}</td>
                            <td class="text-center align-middle">{!! checkIcon($item->ds ?? []) !!}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('kebersihan_ruang.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('kebersihan_ruang.deletePermanent', $item->uuid) }}" 
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
                    <td colspan="20" class="text-center align-middle">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $kebersihan_ruang->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection

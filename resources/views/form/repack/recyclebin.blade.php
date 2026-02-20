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
                <h3><i class="bi bi-trash"></i> Recycle Bin Monitoring Proses Repack</h3>
                <a href="{{ route('repack.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th rowspan="2">NO.</th>
                            <th rowspan="2">Date | Shift</th>
                            <th rowspan="2">Nama Produk</th>
                            <th colspan="2">Kodefikasi</th>
                            <th rowspan="2">Jumlah (Box/Pack)</th>
                            <th rowspan="2">Expired Date</th>
                            <th colspan="4">Kesesuaian*</th>
                            <th rowspan="2">Keterangan</th>
                            <th rowspan="2">Catatan</th>
                            <th rowspan="2">QC</th>
                            <th rowspan="2">SPV</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>Produk</th>
                            <th>Karton</th>
                            <th>Kodefikasi</th>
                            <th>Content/Isi</th>
                            <th>Kerapihan</th>
                            <th>Lain-lain</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($repack as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>   
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                            <td class="text-center align-middle">{{ $item->karton }}</td>
                            <td class="text-center align-middle">{{ $item->jumlah }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                            <td class="text-center align-middle">
                                {!! $item->kodefikasi === 'sesuai' ? '<i class="bi bi-check-circle text-success"></i>' : '-' !!}
                            </td>
                            <td class="text-center align-middle">
                                {!! $item->content === 'sesuai' ? '<i class="bi bi-check-circle text-success"></i>' : '-' !!}
                            </td>
                            <td class="text-center align-middle">
                                {!! $item->kerapihan === 'sesuai' ? '<i class="bi bi-check-circle text-success"></i>' : '-' !!}
                            </td>
                            <td class="text-center align-middle">
                                {!! $item->lainnya === 'sesuai' ? '<i class="bi bi-check-circle text-success"></i>' : '-' !!}
                            </td>
                            <td class="text-center align-middle">{{ $item->keterangan ?: '-' }}</td>
                            <td class="text-center align-middle">{{ $item->catatan ?: '-' }}</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('repack.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('repack.deletePermanent', $item->uuid) }}" 
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
        {{ $repack->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection

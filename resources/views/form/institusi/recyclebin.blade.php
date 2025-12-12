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
                <h3><i class="bi bi-trash"></i> Recycle Bin Verifikasi Institusi</h3>
                <a href="{{ route('institusi.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th rowspan="2">NO.</th>
                            <th rowspan="2">Date | Shift</th>
                            <th rowspan="2">Jenis Produk</th>
                            <th rowspan="2">Kode Produksi</th>
                            <th colspan="2">Proses Thawing</th>
                            <th colspan="2">Suhu Produk (°C)</th>
                            <th rowspan="2">QC</th>
                            <th rowspan="2">Dihapus Pada</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>Waktu Proses</th>
                            <th>Lokasi</th>
                            <th>Sebelum</th>
                            <th>Sesudah</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($institusi as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>

                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}<br>
                                Shift: {{ $item->shift }}
                            </td>

                            <td class="text-center align-middle">{{ $item->jenis_produk }}</td>
                            <td class="text-center align-middle">{{ $item->kode_produksi }}</td>

                            <td class="text-center align-middle">
                                @if($item->waktu_proses_mulai && $item->waktu_proses_selesai)
                                {{ \Carbon\Carbon::parse($item->waktu_proses_mulai)->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($item->waktu_proses_selesai)->format('H:i') }}
                                @else
                                -
                                @endif
                            </td>

                            <td class="text-center align-middle">{{ $item->lokasi }}</td>
                            <td class="text-center align-middle">{{ $item->suhu_sebelum }}°C</td>
                            <td class="text-center align-middle">{{ $item->suhu_sesudah }}°C</td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('institusi.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('institusi.deletePermanent', $item->uuid) }}" 
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
                    <td colspan="10" class="text-center">Recycle bin kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        {{ $institusi->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection

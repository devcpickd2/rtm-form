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
                <h3><i class="bi bi-trash"></i> Recycle Bin Rice Cooker</h3>
                <a href="{{ route('rice.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Nama Produk</th>
                            <th>Rice Cooker</th>
                            <th>QC</th>
                            <th>Deleted at</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($rice as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>
                            <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                            <td class="text-center align-middle">
                                @php
                                $cooker = json_decode($item->cooker, true);
                                @endphp

                                @if(!empty($cooker))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#cookerModal{{ $item->uuid }}" style="font-weight: bold; text-decoration: underline;">
                                    Hasil Pemasakan
                                </a>
                                <div class="modal fade" id="cookerModal{{ $item->uuid }}" tabindex="-1" aria-labelledby="cookerModalLabel{{ $item->uuid }}" aria-hidden="true">
                                    <div class="modal-dialog" style="max-width: 500px;">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title text-start" id="cookerModalLabel{{ $item->uuid }}">Detail Rice Cooker</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm text-left align-middle">
                                                        <tbody>
                                                            <tr>
                                                                <th>Kode Berat</th>
                                                                @foreach($cooker as $items)
                                                                <td>{{ $items['kode_beras'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Berat (Kg)</th>
                                                                @foreach($cooker as $items)
                                                                <td>{{ $items['berat'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Kode Produksi</th>
                                                                @foreach($cooker as $items)
                                                                <td>{{ $items['kode_produksi'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Waktu Cooker (Menit)</th>
                                                                @foreach($cooker as $items)
                                                                <td>{{ $items['waktu_masak'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Suhu Produk (°C)</th>
                                                                @foreach($cooker as $items)
                                                                <td>{{ $items['suhu_produk'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Suhu Produk 1 Menit (°C)</th>
                                                                @foreach($cooker as $items)
                                                                <td>{{ $items['suhu_after'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Suhu After Vacuum (°C)</th>
                                                                @foreach($cooker as $items)
                                                                <td>{{ $items['suhu_vacuum'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Jam Mulai</th>
                                                                @foreach($cooker as $items)
                                                                <td>{{ $items['jam_mulai'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th>Jam Selesai</th>
                                                                @foreach($cooker as $items)
                                                                <td>{{ $items['jam_selesai'] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span>-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">{{ $item->username }}</td>
                            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                            <td class="text-center align-middle">
                                <form action="{{ route('rice.restore', $item->uuid) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm mb-1">
                                        <i class="bi bi-arrow-clockwise"></i> Restore
                                    </button>
                                </form>

                                <form action="{{ route('rice.deletePermanent', $item->uuid) }}" 
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
        {{ $rice->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>

</div>
@endsection

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
                <h3><i class="bi bi-trash"></i> Recycle Bin GMP</h3>
                <a href="{{ route('gmp.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date</th>
                            <th>Noodle & Rice</th>
                            <th>Cooking</th>
                            <th>Packing</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($gmp as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>   
                            @php
                            if (!function_exists('hitungPresentase')) {
                                function hitungPresentase($json) {
                                    if (!$json) return 0;

                                    $data = is_array($json) ? $json : json_decode($json, true);
                                    if (!$data) return 0;

                                    $total = 0;
                                    $count = 0;

                                    foreach ($data as $row) {
                                        foreach ($row as $key => $val) {
                                            if ($key !== 'nama_karyawan') {
                                                $total++;
                                                if ($val == 1) $count++;
                                            }
                                        }
                                    }

                                    return $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                }
                            }

                            if (!function_exists('topKaryawan')) {
                                function topKaryawan($json, $limit = 3) {
                                    if (!$json) return [];

                                    $data = is_array($json) ? $json : json_decode($json, true);
                                    if (!$data) return [];

                                    $scores = [];
                                    foreach ($data as $row) {
                                        $nama = $row['nama_karyawan'] ?? 'Tanpa Nama';
                                        $count = 0;
                                        foreach ($row as $key => $val) {
                                            if ($key !== 'nama_karyawan' && $val == 1) $count++;
                                        }
                                        $scores[] = ['nama' => $nama, 'nilai' => $count];
                                    }

                                    usort($scores, function($a, $b) { return $b['nilai'] <=> $a['nilai']; });

                                        return array_slice($scores, 0, $limit);
                                    }
                                }
                                @endphp

                                {{-- Pemakaian di tabel --}}
                                <td class="text-center align-middle">
                                    {{ hitungPresentase($item->noodle_rice) }} %
                                    <br>
                                    <small>
                                        @foreach(topKaryawan($item->noodle_rice) as $row)
                                        • {{ $row['nama'] }} ({{ $row['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>

                                <td class="text-center align-middle">
                                    {{ hitungPresentase($item->cooking) }} %
                                    <br>
                                    <small>
                                        @foreach(topKaryawan($item->cooking) as $row)
                                        • {{ $row['nama'] }} ({{ $row['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>

                                <td class="text-center align-middle">
                                    {{ hitungPresentase($item->packing) }} %
                                    <br>
                                    <small>
                                        @foreach(topKaryawan($item->packing) as $row)
                                        • {{ $row['nama'] }} ({{ $row['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>
                                <td class="text-center align-middle">{{ $item->username }}</td>
                                <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

                                <td class="text-center align-middle">
                                    <form action="{{ route('gmp.restore', $item->uuid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm mb-1">
                                            <i class="bi bi-arrow-clockwise"></i> Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('gmp.deletePermanent', $item->uuid) }}" 
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
                        <td colspan="8" class="text-center align-middle">Recycle bin kosong.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-2">
            {{ $gmp->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

</div>
@endsection

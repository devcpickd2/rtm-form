@extends('layouts.app')
@php
    $user = auth()->user();

    if (!$user) {
        header('Location: ' . route('login'));
        exit();
    }

    $type_user = $user->type_user;
@endphp
@section('content')
    <div class="container-fluid py-0">
        {{-- Alert sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Alert error (validasi) --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="bi bi-list-check"></i> Data Estafet Alat QC</h3>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('inventaris.create') }}" class="btn btn-success mr-1">
                            <i class="bi bi-plus-circle"></i> Tambah
                        </a>
                        @if (in_array($type_user, [0, 2, 8]))
                            <a href="{{ route('inventaris.recyclebin') }}" class="btn btn-secondary">
                                <i class="bi bi-trash"></i> Recycle Bin
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Filter dan Live Search --}}
                <form id="filterForm" method="GET" action="{{ route('inventaris.index') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">
                    <div class="input-group" style="max-width: 220px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                        <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                            value="{{ request('date') }}">
                    </div>

                    <div class="input-group flex-grow-1" style="max-width: 350px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="search" class="form-control border-start-0"
                            value="{{ request('search') }}" placeholder="Search Area..." autocomplete="off">
                    </div>
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const dateInput = document.getElementById('filter_date');
                        const searchInput = document.getElementById('search');
                        const tableBody = document.getElementById('tableBody');
                        dateInput.addEventListener('change', function() {
                            document.getElementById('filterForm').submit();
                        });
                        searchInput.addEventListener('input', function() {
                            const keyword = this.value.toLowerCase().trim();
                            const rows = tableBody.querySelectorAll('tr');
                            rows.forEach(function(row) {
                                const text = row.textContent.toLowerCase();

                                if (text.includes(keyword)) {
                                    row.style.display = '';
                                } else {
                                    row.style.display = 'none';
                                }
                            });
                        });
                    });
                </script>

                {{-- Tambahkan table-responsive agar tabel tidak keluar border --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Area</th>
                                <th>Detail</th>
                                <th>QC Awal</th>
                                <th>QC Update</th>
                                <th>Keterangan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @php
                                $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                            @endphp
                            @forelse ($data as $dep)
                                <tr>
                                    <td class="text-center align-middle">{{ $no++ }}</td>
                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift:
                                        {{ $dep->shift }}</td>
                                    <td class="text-center align-middle">
                                        {{ match ((int) $dep->area) {
                                            1 => 'Cooking',
                                            2 => 'Packing',
                                            3 => 'Warehouse',
                                            default => '-',
                                        } }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($dep->detail)
                                            @php
                                                $details = is_array($dep->detail)
                                                    ? $dep->detail
                                                    : json_decode($dep->detail, true);
                                            @endphp

                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                data-bs-target="#modalDetail{{ $loop->index }}">
                                                <i class="bi bi-eye"></i> Lihat Detail
                                            </button>

                                            <div class="modal fade" id="modalDetail{{ $loop->index }}" tabindex="-1"
                                                aria-labelledby="modalDetailLabel{{ $loop->index }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info text-white">
                                                            <h6 class="modal-title"
                                                                id="modalDetailLabel{{ $loop->index }}">
                                                                <i class="bi bi-tools"></i> Detail Pemeriksaan Alat
                                                            </h6>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-0">
                                                            <div class="table-responsive">
                                                                <table
                                                                    class="table table-bordered table-sm mb-0 text-center align-middle">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>Nama Alat</th>
                                                                            <th>Jumlah</th>
                                                                            <th>Kondisi Awal</th>
                                                                            <th>Kondisi Akhir</th>
                                                                            <th>Keterangan</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse($details as $i => $alat)
                                                                            <tr>
                                                                                <td>{{ $i + 1 }}</td>
                                                                                <td class="text-start">
                                                                                    {{ $alat['nama_alat'] ?? '-' }}</td>
                                                                                <td>{{ $alat['jumlah'] ?? '-' }}</td>
                                                                                <td>{{ $alat['kondisi_awal'] ?? '-' }}</td>
                                                                                <td>{{ $alat['kondisi_akhir'] ?? '-' }}
                                                                                </td>
                                                                                <td class="text-start">
                                                                                    {{ $alat['keterangan'] ?? '-' }}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="6"
                                                                                    class="text-center text-muted">Tidak ada
                                                                                    data</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm"
                                                                data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">{{ $dep->username ?? '-' }}</td>
                                    <td class="text-center align-middle">{{ $dep->username_updated ?? '-' }}</td>
                                    <td class="text-center align-middle">{{ $dep->keterangan }}</td>

                                    <td class="text-center align-middle">
                                        <a href="{{ route('inventaris.edit', $dep->uuid) }}"
                                            class="btn btn-warning btn-sm me-1">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>
                                        @if (in_array($type_user, [0, 2, 8]))
                                            <form action="{{ route('inventaris.destroy', $dep->uuid) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                        @endif
                                        <!--  <form action="{{ route('inventaris.destroy', $dep->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus?')">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form> -->
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center align-middle">Belum ada data produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Auto-hide alert setelah 3 detik --}}
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
            }
        }, 3000);
    </script>

    {{-- CSS tambahan agar tabel lebih rapi --}}
    <style>
        .table td,
        .table th {
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .text-danger {
            font-weight: bold;
        }

        .text-muted.fst-italic {
            color: #6c757d !important;
            font-style: italic !important;
        }

        .container {
            padding-left: 2px !important;
            padding-right: 2px !important;
        }
    </style>
@endsection

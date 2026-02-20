@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ trim(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-people"></i> Daftar User</h3>
              <!--   <a href="{{ route('user.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah User
                </a> -->
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('user.index') }}" class="mb-3 d-flex justify-content-end">
                <input 
                type="text" 
                name="search" 
                class="form-control me-2" 
                placeholder="Cari user..." 
                value="{{ request('search') }}" 
                style="width: 250px;">   {{-- <=== dibatasi 250px --}}
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i> Cari
                </button>
                @if(request('search'))
                <a href="{{ route('user.index') }}" class="btn btn-outline-secondary ms-2">Reset</a>
                @endif
            </form>

            {{-- Table User --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No. </th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Plant</th>
                            <th>Department</th>
                            <th>Type</th>
                            <!-- <th style="width: 15%;">Aksi</th> -->
                        </tr>
                    </thead>
                    <tbody>
                     @forelse ($users as $index => $user)
                     <tr>
                        {{-- nomor urut sesuai pagination --}}
                        <td class="text-center">
                            {{ $users->firstItem() + $index }}
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->plantRelasi->plant ?? '-' }}</td>
                        <td>{{ $user->departmentRelasi->nama ?? '-' }}</td>
                        <td>
                            @switch($user->type_user)
                            @case(0) Admin @break
                            @case(1) Manager @break
                            @case(2) Supervisor @break
                            @case(3) Foreman/Forelady Produksi @break
                            @case(8) Foreman/Forelady QC @break
                            @case(4) Inspector @break
                            @case(5) Engineer @break
                            @case(6) Warehouse @break
                            @case(7) Lab @break
                            @default -
                            @endswitch
                        </td>
                       <!--  <td class="text-center">
                            <a href="{{ route('user.edit', $user->uuid) }}" class="btn btn-warning btn-sm me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('user.destroy', $user->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td> -->
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada data user.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-end">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>
</div>

{{-- Auto-hide alert setelah 3 detik --}}
<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if(alert){
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
</script>

{{-- Styling pagination --}}
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

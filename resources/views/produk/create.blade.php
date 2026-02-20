@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <h3 class="mb-4">➕ Tambah Produk</h3>

            {{-- Alert error jika validasi gagal --}}
            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ups!</strong> Ada kesalahan pada inputan Anda:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Form Input --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('produk.store') }}" method="POST">
                        @csrf

                        {{-- Nama Produk --}}
                        <div class="mb-3">
                            <label for="nama_produk" class="form-label">Nama Produk</label>
                            <input
                            type="text"
                            name="nama_produk"
                            class="form-control @error('nama_produk') is-invalid @enderror"
                            placeholder="Masukkan Produk Baru"
                            value="{{ old('nama_produk') }}">
                            @error('nama_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Spesifikasi --}}
                        <h5 class="mb-2">Spesifikasi Produk</h5>
                        <div id="spesifikasiWrapper">
                            @if(old('spesifikasi'))
                            @foreach(old('spesifikasi') as $i => $spec)
                            <div class="spesifikasi-item mb-3" data-index="{{ $i }}">
                                <input type="text" name="spesifikasi[{{ $i }}][sub_produk]" value="{{ $spec['sub_produk'] }}" placeholder="Sub Produk" class="form-control mb-1">
                                <input type="text" name="spesifikasi[{{ $i }}][tahapan]" value="{{ $spec['tahapan'] }}" placeholder="Tahapan Proses" class="form-control mb-1">


                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama Bahan</th>
                                            <th>Berat</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($spec['bahan'] as $j => $b)
                                        <tr>
                                            <td><input type="text" name="spesifikasi[{{ $i }}][bahan][{{ $j }}][nama]" value="{{ $b['nama'] }}" class="form-control"></td>
                                            <td><input type="number" step="0.01" name="spesifikasi[{{ $i }}][bahan][{{ $j }}][berat]" value="{{ $b['berat'] }}" class="form-control"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm btn-hapus-bahan">-</button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary btn-sm btn-tambah-bahan">Tambah Bahan</button>
                                <hr>
                            </div>
                            @endforeach
                            @else
                            <div class="spesifikasi-item mb-3" data-index="0">

                                {{-- Sub Produk --}}
                                <input type="text"
                                name="spesifikasi[0][sub_produk]"
                                placeholder="Sub Produk"
                                class="form-control mb-1">

                                {{-- Tahapan --}}
                                <input type="text"
                                name="spesifikasi[0][tahapan]"
                                placeholder="Tahapan Proses"
                                class="form-control mb-1">

                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama Bahan</th>
                                            <th>Berat</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="spesifikasi[0][bahan][0][nama]" class="form-control"></td>
                                            <td><input type="number" step="0.01" name="spesifikasi[0][bahan][0][berat]" class="form-control"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm btn-hapus-bahan">-</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary btn-sm btn-tambah-bahan">Tambah Bahan</button>
                                <hr>
                            </div>
                            @endif
                        </div>

                        <button type="button" id="btnTambahTahapan" class="btn btn-success btn-sm mb-3">Tambah Tahapan</button>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">💾 Simpan</button>
                            <a href="{{ route('produk.index') }}" class="btn btn-secondary">⬅ Kembali</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e){

    // =========================
    // TAMBAH BAHAN
    // =========================
    if (e.target.closest('.btn-tambah-bahan')) {
        let item   = e.target.closest('.spesifikasi-item');
        let tbody  = item.querySelector('tbody');
        let index  = item.dataset.index;
        let bIndex = tbody.children.length;

        let tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text"
                    name="spesifikasi[${index}][bahan][${bIndex}][nama]"
                    class="form-control">
            </td>
            <td>
                <input type="number" step="0.01"
                    name="spesifikasi[${index}][bahan][${bIndex}][berat]"
                    class="form-control">
            </td>
            <td>
                <button type="button"
                    class="btn btn-danger btn-sm btn-hapus-bahan">-</button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    // =========================
    // HAPUS BAHAN
    // =========================
    if (e.target.closest('.btn-hapus-bahan')) {
        let tbody = e.target.closest('tbody');
        if (tbody.children.length > 1) {
            e.target.closest('tr').remove();
        } else {
            alert('Minimal 1 bahan per tahapan');
        }
    }

    // =========================
    // TAMBAH TAHAPAN
    // =========================
    if (e.target.closest('#btnTambahTahapan')) {
        let wrapper = document.getElementById('spesifikasiWrapper');
        let index   = wrapper.querySelectorAll('.spesifikasi-item').length;

        let div = document.createElement('div');
        div.className = 'spesifikasi-item mb-3';
        div.dataset.index = index;

        div.innerHTML = `
            <input type="text"
                name="spesifikasi[${index}][sub_produk]"
                class="form-control mb-1"
                placeholder="Sub Produk">

            <input type="text"
                name="spesifikasi[${index}][tahapan]"
                class="form-control mb-1"
                placeholder="Tahapan Proses">

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Nama Bahan</th>
                        <th>Berat</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text"
                                name="spesifikasi[${index}][bahan][0][nama]"
                                class="form-control">
                        </td>
                        <td>
                            <input type="number" step="0.01"
                                name="spesifikasi[${index}][bahan][0][berat]"
                                class="form-control">
                        </td>
                        <td>
                            <button type="button"
                                class="btn btn-danger btn-sm btn-hapus-bahan">-</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button"
                class="btn btn-primary btn-sm btn-tambah-bahan">
                Tambah Bahan
            </button>
            <hr>
        `;

        wrapper.appendChild(div);
    }
});
</script>

@endsection

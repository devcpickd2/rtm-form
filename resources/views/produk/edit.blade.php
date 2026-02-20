@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <h3 class="mb-4">✏️ Edit Produk</h3>

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

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('produk.update', $produk->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nama Produk --}}
                        <div class="mb-3">
                            <label for="nama_produk" class="form-label">Nama Produk</label>
                            <input
                            type="text"
                            name="nama_produk"
                            class="form-control @error('nama_produk') is-invalid @enderror"
                            value="{{ old('nama_produk', $produk->nama_produk) }}"
                            placeholder="Masukkan Nama Produk">
                            @error('nama_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Spesifikasi Dinamis --}}
                        <div id="spesifikasiWrapper">
                            @php
                            $spesifikasiData = old('spesifikasi', $produk->spesifikasi ?? []);
                            @endphp

                            @foreach ($spesifikasiData as $index => $spes)
                            <div class="card mb-3 spesifikasi-item" data-index="{{ $index }}">
                                <div class="card-body">

                                    <div class="d-flex justify-content-between mb-2">
                                        <h6>Tahapan</h6>
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus-tahapan">
                                            Hapus Tahapan
                                        </button>
                                    </div>

                                    {{-- SUB PRODUK --}}
                                    <input type="text"
                                    name="spesifikasi[{{ $index }}][sub_produk]"
                                    class="form-control mb-2"
                                    placeholder="Sub Produk"
                                    value="{{ $spes['sub_produk'] ?? '' }}">

                                    {{-- TAHAPAN --}}
                                    <input type="text"
                                    name="spesifikasi[{{ $index }}][tahapan]"
                                    class="form-control mb-2"
                                    placeholder="Tahapan Proses"
                                    value="{{ $spes['tahapan'] ?? '' }}">

                                    {{-- BAHAN --}}
                                    <table class="table table-bordered table-sm bahan-table mb-2">
                                        <thead>
                                            <tr>
                                                <th>Nama Bahan</th>
                                                <th>Berat (Kg)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($spes['bahan'] ?? [] as $bIndex => $bahan)
                                            <tr>
                                                <td>
                                                    <input type="text"
                                                    name="spesifikasi[{{ $index }}][bahan][{{ $bIndex }}][nama]"
                                                    class="form-control"
                                                    value="{{ $bahan['nama'] ?? '' }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01"
                                                    name="spesifikasi[{{ $index }}][bahan][{{ $bIndex }}][berat]"
                                                    class="form-control"
                                                    value="{{ $bahan['berat'] ?? '' }}">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm btn-hapus-bahan">
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <button type="button" class="btn btn-success btn-sm btn-tambah-bahan">
                                        Tambah Bahan
                                    </button>
                                </div>
                            </div>
                            @endforeach

                        </div>

                        <button type="button" class="btn btn-primary btn-sm mb-3" id="btnTambahTahapan">Tambah Tahapan</button>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">💾 Update</button>
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

    // ===== TAMBAH TAHAPAN =====
        if(e.target.closest('#btnTambahTahapan')){
            let wrapper = document.getElementById('spesifikasiWrapper');
            let index = wrapper.querySelectorAll('.spesifikasi-item').length;

            let card = document.createElement('div');
            card.className = 'card mb-3 spesifikasi-item';
            card.dataset.index = index;

            card.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h6>Tahapan</h6>
                <button type="button" class="btn btn-danger btn-sm btn-hapus-tahapan">Hapus Tahapan</button>
            </div>

            <input type="text" name="spesifikasi[${index}][sub_produk]" class="form-control mb-2" placeholder="Sub Produk">
            <input type="text" name="spesifikasi[${index}][tahapan]" class="form-control mb-2" placeholder="Tahapan Proses">

            <table class="table table-bordered table-sm mb-2">
                <tbody>
                    <tr>
                        <td><input type="text" name="spesifikasi[${index}][bahan][0][nama]" class="form-control"></td>
                        <td><input type="number" step="0.01" name="spesifikasi[${index}][bahan][0][berat]" class="form-control"></td>
                        <td><button type="button" class="btn btn-danger btn-sm btn-hapus-bahan">Hapus</button></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-success btn-sm btn-tambah-bahan">Tambah Bahan</button>
        </div>
            `;
            wrapper.appendChild(card);
        }

    // ===== HAPUS TAHAPAN + REINDEX =====
        if(e.target.closest('.btn-hapus-tahapan')){
            e.target.closest('.spesifikasi-item').remove();
            reindexSpesifikasi();
        }

    // ===== TAMBAH BAHAN =====
        if(e.target.closest('.btn-tambah-bahan')){
            let card = e.target.closest('.spesifikasi-item');
            let tbody = card.querySelector('tbody');
            let index = card.dataset.index;
            let bIndex = tbody.children.length;

            let tr = document.createElement('tr');
            tr.innerHTML = `
            <td><input type="text" name="spesifikasi[${index}][bahan][${bIndex}][nama]" class="form-control"></td>
            <td><input type="number" step="0.01" name="spesifikasi[${index}][bahan][${bIndex}][berat]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger btn-sm btn-hapus-bahan">Hapus</button></td>
            `;
            tbody.appendChild(tr);
        }

    // ===== HAPUS BAHAN =====
        if(e.target.closest('.btn-hapus-bahan')){
            let tbody = e.target.closest('tbody');
            if(tbody.children.length > 1){
                e.target.closest('tr').remove();
            } else {
                alert('Minimal 1 bahan per tahapan');
            }
        }

        function reindexSpesifikasi(){
            document.querySelectorAll('.spesifikasi-item').forEach((item, i) => {
                item.dataset.index = i;
                item.querySelectorAll('input').forEach(input => {
                    input.name = input.name.replace(/spesifikasi\[\d+]/, `spesifikasi[${i}]`);
                });
            });
        }
    });
</script>


@endsection

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk; 

class ProdukController extends Controller
{
    // Menampilkan semua data produk
    public function index(Request $request)
    {
        $search = $request->input('search');

        $produk = \App\Models\Produk::query()
        ->when($search, function($query, $search) {
            $query->where('nama_produk', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10) // 10 item per halaman
        ->withQueryString(); // agar search tetap tersimpan saat pindah halaman

        return view('produk.index', compact('produk'));
    }

    public function create()
    {
        return view('produk.create'); 
    }

    // Simpan data baru ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',

            'spesifikasi' => 'nullable|array',

            'spesifikasi.*.sub_produk' => 'nullable|string|max:255',
            'spesifikasi.*.tahapan'    => 'required|string|max:255',

            'spesifikasi.*.bahan' => 'required|array|min:1',
            'spesifikasi.*.bahan.*.nama'  => 'required|string|max:255',
            'spesifikasi.*.bahan.*.berat' => 'required|numeric',
        ]);

        Produk::create([
            'username'   => session('username', 'putri'),
            'nama_produk'=> $validated['nama_produk'],
            'spesifikasi'=> $validated['spesifikasi'] ?? null, 
        ]);

        return redirect()
        ->route('produk.index')
        ->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $produk = Produk::where('uuid', $uuid)->firstOrFail();
        return view('produk.edit', compact('produk'));
    }

    public function update(Request $request, $uuid)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',

            'spesifikasi' => 'nullable|array',

            'spesifikasi.*.sub_produk' => 'nullable|string|max:255',
            'spesifikasi.*.tahapan'    => 'required|string|max:255',

            'spesifikasi.*.bahan' => 'required|array|min:1',
            'spesifikasi.*.bahan.*.nama'  => 'required|string|max:255',
            'spesifikasi.*.bahan.*.berat' => 'required|numeric',
        ]);

        $produk = Produk::where('uuid', $uuid)->firstOrFail();

        $produk->update([
            'nama_produk'=> $validated['nama_produk'],
            'spesifikasi'=> $validated['spesifikasi'] ?? null,
        ]);

        return redirect()
        ->route('produk.index')
        ->with('success', 'Produk berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $produk = Produk::where('uuid', $uuid)->firstOrFail();
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }

    public function recyclebin()
    {
        $produk = Produk::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('produk.recyclebin', compact('produk'));
    }
    public function restore($uuid)
    {
        $produk = Produk::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $produk->restore();

        return redirect()->route('produk.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $produk = Produk::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $produk->forceDelete();

        return redirect()->route('produk.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi; 

class ProduksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $produksi = \App\Models\Produksi::query()
        ->when($search, function($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                ->orWhere('area', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('produksi.index', compact('produksi'));
    }

    public function create()
    {
        return view('produksi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'area' => 'required|string|max:255'
        ]);

        Produksi::create([
            'nama_karyawan' => $request->nama_karyawan,
            'area' => $request->area
        ]);

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $produksi = Produksi::where('uuid', $uuid)->firstOrFail();
        return view('produksi.edit', compact('produksi'));
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'area' => 'required|string|max:255'
        ]);

        $produksi = Produksi::where('uuid', $uuid)->firstOrFail();

        $produksi->update([
            'nama_karyawan' => $request->nama_karyawan,
            'area' => $request->area
        ]);

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $produksi = Produksi::where('uuid', $uuid)->firstOrFail();
        $produksi->delete();
        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil dihapus');
    }

    public function recyclebin()
    {
        $produksi = Produksi::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('produksi.recyclebin', compact('produksi'));
    }
    public function restore($uuid)
    {
        $produksi = Produksi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $produksi->restore();

        return redirect()->route('produksi.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $produksi = Produksi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $produksi->forceDelete();

        return redirect()->route('produksi.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
}

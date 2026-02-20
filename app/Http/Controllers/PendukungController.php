<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendukung; 

class PendukungController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pendukung = \App\Models\Pendukung::query()
        ->when($search, function($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                ->orWhere('area', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('pendukung.index', compact('pendukung'));
    }

    public function create()
    {
        return view('pendukung.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'area' => 'required|string|max:255'
        ]);

        Pendukung::create([
            'nama_karyawan' => $request->nama_karyawan,
            'area' => $request->area
        ]);

        return redirect()->route('pendukung.index')->with('success', 'Karyawan Pendukung berhasil ditambahkan');
    }

    public function edit($uuid)
    {
        $pendukung = Pendukung::where('uuid', $uuid)->firstOrFail();
        return view('pendukung.edit', compact('pendukung'));
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'area' => 'required|string|max:255'
        ]);

        $pendukung = Pendukung::where('uuid', $uuid)->firstOrFail();

        $pendukung->update([
            'nama_karyawan' => $request->nama_karyawan,
            'area' => $request->area
        ]);

        return redirect()->route('pendukung.index')->with('success', 'Karyawan Pendukung berhasil diupdate');
    }

    public function destroy($uuid)
    {
        $pendukung = Pendukung::where('uuid', $uuid)->firstOrFail();
        $pendukung->delete();
        return redirect()->route('pendukung.index')->with('success', 'Karyawan Pendukung berhasil dihapus');
    }

    public function recyclebin()
    {
        $pendukung = Pendukung::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('pendukung.recyclebin', compact('pendukung'));
    }
    public function restore($uuid)
    {
        $pendukung = Pendukung::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pendukung->restore();

        return redirect()->route('pendukung.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $pendukung = Pendukung::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pendukung->forceDelete();

        return redirect()->route('pendukung.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
}

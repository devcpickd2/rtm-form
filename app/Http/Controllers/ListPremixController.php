<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListPremix;

class ListPremixController extends Controller
{
 public function index(Request $request)
 {
    $search = $request->input('search');

    $listpremix = \App\Models\ListPremix::query()
    ->when($search, function($query, $search) {
        $query->where('nama_premix', 'like', "%{$search}%")
        ->orWhere('alergen', 'like', "%{$search}%");
    })
    ->orderBy('created_at', 'desc')
    ->paginate(10) 
    ->withQueryString(); 

    return view('listpremix.index', compact('listpremix'));
}

public function create()
{
    return view('listpremix.create');
}

public function store(Request $request)
{
    $request->validate([
        'nama_premix' => 'required|string|max:255|unique:list_premixes,nama_premix',
        'alergen' => 'required|string|max:255'
    ]);

    $username = session('username', 'user');

    ListPremix::create([
        'username' => $username,
        'nama_premix' => $request->nama_premix,
        'alergen' => $request->alergen
    ]);

    return redirect()->route('listpremix.index')->with('success', 'Premix berhasil ditambahkan');
}

public function edit($uuid)
{
    $listpremix = ListPremix::where('uuid', $uuid)->firstOrFail();
    return view('listpremix.edit', compact('listpremix'));
}

public function update(Request $request, $uuid)
{
    $listpremix = ListPremix::where('uuid', $uuid)->firstOrFail();

    $request->validate([
        'nama_premix' => 'required|string|max:255|unique:list_premixes,nama_premix,' . $listpremix->id,
        'alergen' => 'required|string|max:255'
    ]);

    $listpremix->update([
        'nama_premix' => $request->nama_premix,
        'alergen' => $request->alergen
    ]);

    return redirect()->route('listpremix.index')->with('success', 'Premix berhasil diupdate');
}

public function destroy($uuid)
{
    $listpremix = ListPremix::where('uuid', $uuid)->firstOrFail();
    $listpremix->delete(); // sudah soft delete otomatis
    return redirect()->route('listpremix.index')->with('success', 'Premix berhasil dihapus');
}

public function recyclebin()
{
    $listpremix = ListPremix::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('listpremix.recyclebin', compact('listpremix'));
}
public function restore($uuid)
{
    $listpremix = ListPremix::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $listpremix->restore();

    return redirect()->route('listpremix.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $listpremix = ListPremix::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $listpremix->forceDelete();

    return redirect()->route('listpremix.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

}

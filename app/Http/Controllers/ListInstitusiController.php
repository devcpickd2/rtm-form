<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListInstitusi;

class ListInstitusiController extends Controller
{
   public function index(Request $request)
   {
    $search = $request->input('search');

    $listinstitusi = \App\Models\ListInstitusi::query()
    ->when($search, function($query, $search) {
        $query->where('nama_institusi', 'like', "%{$search}%");
    })
    ->orderBy('created_at', 'desc')
    ->paginate(10) 
    ->withQueryString(); 

    return view('listinstitusi.index', compact('listinstitusi'));
}

public function create()
{
    return view('listinstitusi.create');
}

public function store(Request $request)
{
    $request->validate([
        'nama_institusi' => 'required|string|max:255',
    ]);

    $username = session('username', 'user');

    ListInstitusi::create([
        'username' => $username,
        'nama_institusi' => $request->nama_institusi
    ]);

    return redirect()->route('listinstitusi.index')->with('success', 'Institusi berhasil ditambahkan');
}

public function edit($uuid)
{
    $listinstitusi = ListInstitusi::where('uuid', $uuid)->firstOrFail();
    return view('listinstitusi.edit', compact('listinstitusi'));
}

public function update(Request $request, $uuid)
{
    $request->validate([
        'nama_institusi' => 'required|string|max:255'
    ]);

    $listinstitusi = ListInstitusi::where('uuid', $uuid)->firstOrFail();
    $listinstitusi->update([
        'nama_institusi' => $request->nama_institusi
    ]);

    return redirect()->route('listinstitusi.index')->with('success', 'Institusi berhasil diupdate');
}

public function destroy($uuid)
{
    $listinstitusi = ListInstitusi::where('uuid', $uuid)->firstOrFail();
    $listinstitusi->delete(); // sudah soft delete otomatis
    return redirect()->route('listinstitusi.index')->with('success', 'Institusi berhasil dihapus');
}

public function recyclebin()
{
    $listinstitusi = ListInstitusi::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('listinstitusi.recyclebin', compact('listinstitusi'));
}
public function restore($uuid)
{
    $listinstitusi = ListInstitusi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $listinstitusi->restore();

    return redirect()->route('listinstitusi.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $listinstitusi = ListInstitusi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $listinstitusi->forceDelete();

    return redirect()->route('listinstitusi.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}

}

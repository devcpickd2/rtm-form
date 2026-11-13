<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estafet;

class EstafetController extends Controller
{
   public function index(Request $request)
   {
    $search = $request->input('search');

    $estafet = \App\Models\Estafet::query()
    ->when($search, function($query, $search) {
        $query->where('area', 'like', "%{$search}%")
        ->orWhere('nama_alat', 'like', "%{$search}%");
    })
    ->orderBy('created_at', 'desc')
    ->paginate(10) 
    ->withQueryString(); 

    return view('estafet.index', compact('estafet'));
}

public function create()
{
    return view('estafet.create');
}

public function store(Request $request)
{
    $request->validate([
        'area' => 'required|string|max:255',
        'nama_alat' => 'required|string|max:255',
        'jumlah' => 'required|integer',        
    ]);

    $username = session('username', 'user');

    Estafet::create([
        'username' => $username,
        'area' => $request->area,
        'nama_alat' => $request->nama_alat,
        'jumlah' => $request->jumlah
    ]);

    return redirect()->route('estafet.index')->with('success', 'Alat QC Berhasil Ditambahkan');
}

public function edit($uuid)
{
    $estafet = Estafet::where('uuid', $uuid)->firstOrFail();
    return view('estafet.edit', compact('estafet'));
}

public function update(Request $request, $uuid)
{
    $username = session('username', 'user');
    $estafet = Estafet::where('uuid', $uuid)->firstOrFail();

    $request->validate([
        'area' => 'required|string|max:255',
        'nama_alat' => 'required|string|max:255',
        'jumlah' => 'required|integer',        
    ]);

    $estafet->update([
        'username' => $username,
        'area' => $request->area,
        'nama_alat' => $request->nama_alat,
        'jumlah' => $request->jumlah
    ]);

    return redirect()->route('estafet.index')->with('success', 'Alat QC Berhasil Diupdate');
}

public function destroy($uuid)
{
    $estafet = Estafet::where('uuid', $uuid)->firstOrFail();
    $estafet->delete(); 
    return redirect()->route('estafet.index')->with('success', 'Alat QC berhasil dihapus');
}

public function recyclebin()
{
    $estafet = Estafet::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('estafet.recyclebin', compact('estafet'));
}
public function restore($uuid)
{
    $estafet = Estafet::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $estafet->restore();

    return redirect()->route('estafet.recyclebin')
    ->with('success', 'Alat QC berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $estafet = Estafet::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $estafet->forceDelete();

    return redirect()->route('estafet.recyclebin')
    ->with('success', 'Alat QC berhasil dihapus permanen.');
}

}

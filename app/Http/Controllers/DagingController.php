<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Daging;
use Illuminate\Validation\Rule;

class DagingController extends Controller
{
   public function index(Request $request)
   {
    $search = $request->input('search');

    $daging = \App\Models\Daging::query()
    ->when($search, function($query, $search) {
        $query->where('nama_daging', 'like', "%{$search}%");
    })
    ->orderBy('created_at', 'desc')
    ->paginate(10) 
    ->withQueryString(); 

    return view('daging.index', compact('daging'));
}

public function create()
{
    return view('daging.create');
}

public function store(Request $request)
{
    $request->validate([
        'nama_daging' => [
            'required',
            'string',
            'max:255',
            Rule::unique('dagings', 'nama_daging'),
        ],
    ], [
        'nama_daging.required' => 'Nama daging wajib diisi.',
        'nama_daging.unique'   => 'Nama daging sudah terdaftar.',
    ]);

    $username = session('username', 'user');

    Daging::create([
        'username' => $username,
        'nama_daging' => $request->nama_daging
    ]);

    return redirect()->route('daging.index')->with('success', 'List Daging Berhasil Ditambahkan');
}

public function edit($uuid)
{
    $daging = Daging::where('uuid', $uuid)->firstOrFail();
    return view('daging.edit', compact('daging'));
}

public function update(Request $request, $uuid)
{
    $username = session('username', 'user');
    $daging = Daging::where('uuid', $uuid)->firstOrFail();

    $request->validate([
        'nama_daging' => [
            'required',
            'string',
            'max:255',
            Rule::unique('dagings', 'nama_daging'),
        ],
    ], [
        'nama_daging.required' => 'Nama daging wajib diisi.',
        'nama_daging.unique'   => 'Nama daging sudah terdaftar.',
    ]);

    $daging->update([
        'username' => $username,
        'nama_daging' => $request->nama_daging
    ]);

    return redirect()->route('daging.index')->with('success', 'List Daging Berhasil Diupdate');
}

public function destroy($uuid)
{
    $daging = Daging::where('uuid', $uuid)->firstOrFail();
    $daging->delete(); 
    return redirect()->route('daging.index')->with('success', 'List Daging berhasil dihapus');
}

public function recyclebin()
{
    $daging = Daging::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('daging.recyclebin', compact('daging'));
}
public function restore($uuid)
{
    $daging = Daging::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $daging->restore();

    return redirect()->route('daging.recyclebin')
    ->with('success', 'List Daging berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $daging = Daging::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $daging->forceDelete();

    return redirect()->route('daging.recyclebin')
    ->with('success', 'List Daging berhasil dihapus permanen.');
}

}

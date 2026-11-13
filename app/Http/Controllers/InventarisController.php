<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Estafet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class InventarisController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search'));
        $date   = $request->input('date');

        $data = Inventaris::query()
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })

        ->when($search, function ($query) use ($search) {
            $areaMap = [
                'cooking'   => 1,
                'packing'   => 2,
                'warehouse' => 3,
            ];

            $searchLower = strtolower($search);
            if (isset($areaMap[$searchLower])) {
                $query->where('area', $areaMap[$searchLower]);
            }
        })

        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('form.inventaris.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $estafets = Estafet::all();
        return view('form.inventaris.create', compact('estafets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'area'          => 'required',
            'detail'        => 'nullable|array',
            'keterangan'    => 'nullable|string',
        ]);

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'area'             => $request->area,
            'keterangan'       => $request->keterangan,
            'detail'           => json_encode($request->input('detail', []), JSON_UNESCAPED_UNICODE),
        ];

        $data['username']  = Auth::user()->username;
        $inventaris = Inventaris::create($data);

        return redirect()->route('inventaris.index')
        ->with('success', 'Data Estafet QC Berhasil Ditambahkan');
    }

    public function edit($uuid)
    {
        $inventaris = Inventaris::where('uuid', $uuid)->firstOrFail();
        $estafets = Estafet::all();

        return view('form.inventaris.edit', compact( 
            'inventaris',
            'estafets'
        ));
    }

    public function update(Request $request, $uuid)
    {
        $inventaris = Inventaris::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'area'          => 'required',
            'detail'        => 'nullable|array',
            'keterangan'    => 'nullable|string',
        ]);

        $data = [
          'date'             => $request->date,
          'shift'            => $request->shift,
          'area'             => $request->area,
          'keterangan'       => $request->keterangan,
          'detail'           => json_encode($request->input('detail', []), JSON_UNESCAPED_UNICODE)
      ];

      $data['username_updated'] = Auth::user()->username;
      $inventaris->update($data);
      return redirect()->route('inventaris.index')
      ->with('success', 'Data Estafet Alat QC berhasil diperbarui');
  }

  public function verification(Request $request)
  {
    $search = $request->input('search');
    $date = $request->input('date');

    $data = Inventaris::query()
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })

    ->when($search, function ($query) use ($search) {
        $areaMap = [
            'cooking'   => 1,
            'packing'   => 2,
            'warehouse' => 3,
        ];

        $searchLower = strtolower($search);
        if (isset($areaMap[$searchLower])) {
            $query->where('area', $areaMap[$searchLower]);
        }
    })
    
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    return view('form.inventaris.verification', compact('data', 'search', 'date'));
}

public function destroy($uuid)
{
    $inventaris = Inventaris::where('uuid', $uuid)->firstOrFail();
    $inventaris->delete();
    return redirect()->route('inventaris.verification')->with('success', 'inventaris berhasil dihapus');
}

public function recyclebin()
{
    $inventaris = Inventaris::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.inventaris.recyclebin', compact('inventaris'));
}
public function restore($uuid)
{
    $inventaris = Inventaris::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $inventaris->restore();

    return redirect()->route('inventaris.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $inventaris = Inventaris::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $inventaris->forceDelete();

    return redirect()->route('inventaris.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}
}
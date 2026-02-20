<?php

namespace App\Http\Controllers;

use App\Models\Retain;
use App\Models\Produk;
use App\Models\Pendukung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RetainController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = retain::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('production_code', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.retain.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        $warehouses = Pendukung::where('area', 'Warehouse')->get();
        return view('form.retain.create', compact('produks', 'warehouses'));
    }

    public function store(Request $request)
    {
       $username = Auth::user()->username;

       $request->validate([
        'date' => 'required|date',
        'plant' => 'required|string|max:255',
        'sample_type' => 'required|string|max:255',
        'sample_storage' => 'nullable|array',
        'description' => 'required|string|max:255',
        'production_code' => 'required|string|max:255',
        'best_before' => 'required|date',
        'quantity' => 'nullable|integer',
        'remarks' => 'nullable|string',
        'note' => 'nullable|string',
        'nama_warehouse' => 'required',
    ]);

       $data = $request->only([
        'date','plant','sample_type','sample_storage','description','production_code',
        'best_before','quantity','remarks','note', 'nama_warehouse'
    ]);

       $data['username'] = $username;
       $data['status_warehouse'] = "1";
       $data['status_spv'] = "0";

       $data['sample_storage'] = json_encode($request->input('sample_storage', []), JSON_UNESCAPED_UNICODE);

       Retain::create($data);

       return redirect()->route('retain.index')
       ->with('success', 'Data Retained Sample Report berhasil disimpan');
   }


   public function edit($uuid)
   {
    $retain = Retain::where('uuid', $uuid)->firstOrFail();
    $produks = Produk::all();
    $warehouses = Pendukung::where('area', 'Warehouse')->get();
    $selectedStorage = json_decode($retain->sample_storage, true) ?? [];

    return view('form.retain.edit', compact('retain', 'produks', 'selectedStorage', 'warehouses'));
}

public function update(Request $request, $uuid)
{
    $retain = Retain::where('uuid', $uuid)->firstOrFail();
    $username_updated = Auth::user()->username;

    $request->validate([
        'date' => 'required|date',
        'plant' => 'required|string|max:255',
        'sample_type' => 'required|string|max:255',
        'sample_storage' => 'nullable|array',
        'description' => 'required|string|max:255',
        'production_code' => 'required|string|max:255',
        'best_before' => 'required|date',
        'quantity' => 'nullable|integer',
        'remarks' => 'nullable|string',
        'note' => 'nullable|string',
        'nama_warehouse' => 'required',
    ]);

    $data = [
        'date'             => $request->date,
        'plant'            => $request->plant,
        'sample_type'      => $request->sample_type,
        'description'      => $request->description,
        'production_code'  => $request->production_code,
        'best_before'      => $request->best_before,
        'quantity'         => $request->quantity,
        'remarks'          => $request->remarks,
        'note'             => $request->note,
        'username_updated' => $username_updated,
        'nama_warehouse'   => $request->nama_warehouse,
        'sample_storage'   => json_encode($request->input('sample_storage', []), JSON_UNESCAPED_UNICODE),
    ];

    $retain->update($data);

    return redirect()->route('retain.index')
    ->with('success', 'Data Retained Sample Report berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = retain::query()
    ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orWhere('production_code', 'like', "%{$search}%");
        });
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    return view('form.retain.verification', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $retain = Retain::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $retain->status_spv = $request->status_spv;
    $retain->catatan_spv = $request->catatan_spv;
    $retain->nama_spv = Auth::user()->username;
    $retain->tgl_update_spv = now();
    $retain->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('retain.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $retain = Retain::where('uuid', $uuid)->firstOrFail();
    $retain->delete();
    return redirect()->route('retain.verification')->with('success', 'Retain Sample berhasil dihapus');
}

public function recyclebin()
{
    $retain = Retain::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.retain.recyclebin', compact('retain'));
}
public function restore($uuid)
{
    $retain = Retain::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $retain->restore();

    return redirect()->route('retain.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $retain = Retain::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $retain->forceDelete();

    return redirect()->route('retain.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}
}

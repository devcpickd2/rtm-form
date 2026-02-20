<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReturController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Retur::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10) 
        ->appends($request->all());

        return view('form.retur.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.retur.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'no_mobil' => 'nullable|string',
            'nama_supir' => 'nullable|string',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'expired_date' => 'nullable|date',
            'jumlah' => 'nullable|integer',
            'bocor' => 'nullable|string',
            'isi_kurang' => 'nullable|string',
            'lainnya' => 'nullable|string',
            'keterangan'   => 'nullable|string',
            'catatan'    => 'nullable|string',
            'nama_warehouse' => 'required',
        ]);

        $data = $request->only([
            'date', 'shift', 'no_mobil', 'nama_supir',
            'nama_produk', 'kode_produksi', 'expired_date', 'jumlah', 'bocor', 'isi_kurang', 'lainnya',
            'keterangan', 'catatan', 'nama_warehouse'
        ]);

        $data['username']      = $username;
        $data['status_warehouse'] = "1";
        $data['status_spv'] = "0";

        Retur::create($data);

        return redirect()->route('retur.index')->with('success', 'Data Pemeriksaan Produk Retur berhasil disimpan');
    }

    public function edit(string $uuid)
    {
       $produks = Produk::all();
       $retur = Retur::where('uuid', $uuid)->firstOrFail();
       return view('form.retur.edit', compact('retur', 'produks'));
   }

   public function update(Request $request, string $uuid)
   {
    $retur = Retur::where('uuid', $uuid)->firstOrFail();

    $username_updated = Auth::user()->username;

    $request->validate([
        'date'  => 'required|date',
        'shift' => 'required',
        'no_mobil' => 'nullable|string',
        'nama_supir' => 'nullable|string',
        'nama_produk' => 'required',
        'kode_produksi' => 'required',
        'expired_date' => 'nullable|date',
        'jumlah' => 'nullable|integer',
        'bocor' => 'nullable|string',
        'isi_kurang' => 'nullable|string',
        'lainnya' => 'nullable|string',
        'keterangan'   => 'nullable|string',
        'catatan'    => 'nullable|string',
        'nama_warehouse' => 'required',
    ]);

    $data = $request->only([
        'date', 'shift', 'no_mobil', 'nama_supir',
        'nama_produk', 'kode_produksi', 'expired_date', 'jumlah', 'bocor', 'isi_kurang', 'lainnya',
        'keterangan', 'catatan', 'nama_warehouse'
    ]);

    $data['username_updated'] = $username_updated;

    $retur->update($data);

    return redirect()->route('retur.index')->with('success', 'Data Pemeriksaan Produk Retur berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Retur::query()
    ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('nama_produk', 'like', "%{$search}%")
        ->orWhere('kode_produksi', 'like', "%{$search}%");
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10) 
    ->appends($request->all());

    return view('form.retur.verification', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $retur = Retur::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $retur->status_spv = $request->status_spv;
    $retur->catatan_spv = $request->catatan_spv;
    $retur->nama_spv = Auth::user()->username;
    $retur->tgl_update_spv = now();
    $retur->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('retur.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $retur = Retur::where('uuid', $uuid)->firstOrFail();
    $retur->delete();
    return redirect()->route('retur.verification')->with('success', 'Retur berhasil dihapus');
}

public function recyclebin()
{
    $retur = Retur::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.retur.recyclebin', compact('retur'));
}
public function restore($uuid)
{
    $retur = Retur::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $retur->restore();

    return redirect()->route('retur.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $retur = Retur::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $retur->forceDelete();

    return redirect()->route('retur.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}
}

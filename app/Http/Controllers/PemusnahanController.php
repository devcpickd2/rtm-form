<?php

namespace App\Http\Controllers;

use App\Models\Pemusnahan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PemusnahanController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Pemusnahan::query()
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

        return view('form.pemusnahan.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.pemusnahan.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

        $request->validate([
            'date'  => 'required|date',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'expired_date'  => 'required|date',
            'analisis' => 'nullable|string',
            'keterangan'   => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'nama_produk', 'kode_produksi', 'expired_date', 'analisis', 'keterangan'
        ]);

        $data['username']      = $username;
        $data['status_spv'] = "0";

        Pemusnahan::create($data);

        return redirect()->route('pemusnahan.index')->with('success', 'Data Pemusnahan Barang/Produk berhasil disimpan');
    }

    public function edit(string $uuid)
    {
       $produks = Produk::all();
       $pemusnahan = pemusnahan::where('uuid', $uuid)->firstOrFail();
       return view('form.pemusnahan.edit', compact('pemusnahan', 'produks'));
   }

   public function update(Request $request, string $uuid)
   {
    $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();

    $username_updated = session('username_updated', 'Harnis');

    $request->validate([
        'date'  => 'required|date',
        'nama_produk' => 'required',
        'kode_produksi' => 'required',
        'expired_date'  => 'required|date',
        'analisis' => 'nullable|string',
        'keterangan'   => 'nullable|string',
    ]);

    $data = $request->only([
        'date', 'nama_produk', 'kode_produksi', 'expired_date', 'analisis', 'keterangan'
    ]);

    $data['username_updated'] = Auth::user()->username;

    $pemusnahan->update($data);

    return redirect()->route('pemusnahan.index')->with('success', 'Data Pemusnahan Barang/Produk berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Pemusnahan::query()
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

    return view('form.pemusnahan.verification', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $pemusnahan->status_spv = $request->status_spv;
    $pemusnahan->catatan_spv = $request->catatan_spv;
    $pemusnahan->nama_spv = Auth::user()->username;
    $pemusnahan->tgl_update_spv = now();
    $pemusnahan->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('pemusnahan.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $pemusnahan = Pemusnahan::where('uuid', $uuid)->firstOrFail();
    $pemusnahan->delete();

    return redirect()->route('pemusnahan.index')->with('success', 'Data Pemusnahan Barang/Produk berhasil dihapus');
}
}

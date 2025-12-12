<?php

namespace App\Http\Controllers;

use App\Models\Cooking;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CookingController extends Controller
{
  public function index(Request $request)
  {
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Cooking::query()
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

    $data->getCollection()->transform(function ($item) {
        $item->pemasakan_decoded = json_decode($item->pemasakan, true) ?? [];
        return $item;
    });

    return view('form.cooking.index', compact('data', 'search', 'date'));
}

public function create()
{
    $produks = Produk::all();
    return view('form.cooking.create', compact('produks'));
}

public function store(Request $request)
{
    $request->validate([
        'date'          => 'required|date',
        'shift'         => 'required',
        'nama_produk'   => 'required',
        'sub_produk'    => 'nullable|string',
        'jenis_produk'  => 'required',
        'kode_produksi' => 'required',
        'waktu_mulai'   => 'nullable',
        'waktu_selesai' => 'nullable',
        'nama_mesin'    => 'required|array',
        'catatan'       => 'nullable|string',
        'pemasakan'     => 'nullable|array',
    ]);

    $data = [
        'date'             => $request->date,
        'shift'            => $request->shift,
        'nama_produk'      => $request->nama_produk,
        'sub_produk'       => $request->sub_produk,
        'jenis_produk'     => $request->jenis_produk,
        'kode_produksi'    => $request->kode_produksi,
        'waktu_mulai'      => $request->waktu_mulai,
        'waktu_selesai'    => $request->waktu_selesai,
        'nama_mesin'       => json_encode($request->input('nama_mesin', []), JSON_UNESCAPED_UNICODE),
        'catatan'          => $request->catatan,
            // encode pemasakan ke JSON
        'pemasakan'        => json_encode($request->input('pemasakan', []), JSON_UNESCAPED_UNICODE),
    ];

    $data['username']         = Auth::user()->username;
    $data['nama_produksi']    = session()->has('selected_produksi') 
    ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name 
    : null;
    $data['status_produksi']  = "1";
    $data['status_spv']       = "0";
    $cooking = Cooking::create($data);

    $cooking->update(['tgl_update_produksi' => Carbon::parse($cooking->created_at)->addHour()]);

    return redirect()->route('cooking.index')
    ->with('success', 'Data Pemeriksaan Pemasakan Produk di Steam/Cooking Kettle berhasil disimpan');
}

public function edit($uuid)
{
    $cooking = Cooking::where('uuid', $uuid)->firstOrFail();
    $produks = Produk::all();

    // decode nama_mesin ke array
    $selectedMesins = json_decode($cooking->nama_mesin, true) ?? [];

    // decode pemasakan juga
    $pemasakanData = json_decode($cooking->pemasakan, true) ?? [];

    return view('form.cooking.edit', compact('cooking', 'produks', 'pemasakanData', 'selectedMesins'));
}

public function update(Request $request, $uuid)
{
    $cooking = Cooking::where('uuid', $uuid)->firstOrFail();
    $username_updated = session('username_updated', 'Harnis');
    $nama_produksi    = session('nama_produksi', 'Produksi RTM');

    $request->validate([
        'date'          => 'required|date',
        'shift'         => 'required',
        'nama_produk'   => 'required',
        'sub_produk'    => 'nullable|string',
        'jenis_produk'  => 'required',
        'kode_produksi' => 'required',
        'waktu_mulai'   => 'nullable',
        'waktu_selesai' => 'nullable',
        'nama_mesin'    => 'required|array',
        'catatan'       => 'nullable|string',
        'pemasakan'     => 'nullable|array',
    ]);

    $data = [
        'date'             => $request->date,
        'shift'            => $request->shift,
        'nama_produk'      => $request->nama_produk,
        'sub_produk'       => $request->sub_produk,
        'jenis_produk'     => $request->jenis_produk,
        'kode_produksi'    => $request->kode_produksi,
        'waktu_mulai'      => $request->waktu_mulai,
        'waktu_selesai'    => $request->waktu_selesai,
        'nama_mesin'       => json_encode($request->input('nama_mesin', []), JSON_UNESCAPED_UNICODE),
        'catatan'          => $request->catatan,
        'username_updated' => $username_updated,
        'nama_produksi'    => $nama_produksi,
            // encode pemasakan ke JSON
        'pemasakan'        => json_encode($request->input('pemasakan', []), JSON_UNESCAPED_UNICODE),
    ];

    $data['username_updated'] = Auth::user()->username;
    $data['nama_produksi']    = session()->has('selected_produksi') 
    ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name 
    : null;
    $cooking->update($data);
    $cooking->update(['tgl_update_produksi' => Carbon::parse($cooking->updated_at)->addHour()]);
    return redirect()->route('cooking.index')
    ->with('success', 'Data Pemeriksaan Pemasakan Produk berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Cooking::query()
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

    $data->getCollection()->transform(function ($item) {
        $item->pemasakan_decoded = json_decode($item->pemasakan, true) ?? [];
        return $item;
    });

    return view('form.cooking.verification', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $cooking = Cooking::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $cooking->status_spv = $request->status_spv;
    $cooking->catatan_spv = $request->catatan_spv;
    $cooking->nama_spv = Auth::user()->username;
    $cooking->tgl_update_spv = now();
    $cooking->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('cooking.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}


public function destroy($uuid)
{
    $cooking = Cooking::where('uuid', $uuid)->firstOrFail();
    $cooking->delete();

    return redirect()->route('cooking.index')
    ->with('success', 'Data Pemeriksaan Pemasakan Produk di Steam/Cooking Kettle berhasil dihapus');
}
}

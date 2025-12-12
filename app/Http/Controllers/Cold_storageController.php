<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cold_storage;
use App\Models\Produk;
use Carbon\Carbon;

class Cold_storageController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Cold_storage::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('suhu_cs', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.cold_storage.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.cold_storage.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'           => 'required|date',
            'shift'          => 'required',
            'pukul'          => 'required',
            'catatan'        => 'nullable|string',
            'nama_warehouse' => 'required',
            'suhu_cs'        => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'pukul', 'catatan', 'nama_warehouse']); 
        $data['username'] = Auth::user()->username;
        $data['suhu_cs']  = json_encode($request->input('suhu_cs', []), JSON_UNESCAPED_UNICODE);
        $data['status_warehouse'] = "1";

        $cold_storage = Cold_storage::create($data);

        // set tgl_update_warehouse = created_at + 1 jam
        $cold_storage->update([
            'tgl_update_warehouse' => Carbon::parse($cold_storage->created_at)->addHour()
        ]);

        return redirect()->route('cold_storage.index')
        ->with('success', 'Data Pemantauan Suhu Produk di Cold Storage berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $cold_storage = Cold_storage::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();
        $suhuData = !empty($cold_storage->suhu_cs) ? json_decode($cold_storage->suhu_cs, true) : [];

        return view('form.cold_storage.edit', compact('cold_storage', 'produks', 'suhuData'));
    }

    public function update(Request $request, string $uuid)
    {
        $cold_storage = Cold_storage::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'           => 'required|date',
            'shift'          => 'required',
            'pukul'          => 'required',
            'catatan'        => 'nullable|string',
            'nama_warehouse' => 'required',
            'suhu_cs'        => 'nullable|array',
        ]);

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'pukul'            => $request->pukul,
            'catatan'          => $request->catatan,
            'nama_warehouse'   => $request->nama_warehouse,
            'username_updated' => Auth::user()->username, // ambil dari login
            'suhu_cs'          => json_encode($request->input('suhu_cs', []), JSON_UNESCAPED_UNICODE),
        ];

        $cold_storage->update($data);

        // update tgl_update_warehouse = updated_at + 1 jam
        $cold_storage->update([
            'tgl_update_warehouse' => Carbon::parse($cold_storage->updated_at)->addHour()
        ]);

        return redirect()->route('cold_storage.index')
        ->with('success', 'Data Pemantauan Suhu Produk di Cold Storage berhasil diperbarui');
    }

    public function verification(Request $request)
    {
      $search     = $request->input('search');
      $date = $request->input('date');

      $data = Cold_storage::query()
      ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('suhu_cs', 'like', "%{$search}%");
    })
      ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
      ->orderBy('date', 'desc')
      ->orderBy('created_at', 'desc')
      ->paginate(10)
      ->appends($request->all());

      return view('form.cold_storage.verification', compact('data', 'search', 'date'));
  }

  public function updateVerification(Request $request, $uuid)
  {
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $cold_storage = Cold_storage::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $cold_storage->status_spv = $request->status_spv;
    $cold_storage->catatan_spv = $request->catatan_spv;
    $cold_storage->nama_spv = Auth::user()->username;
    $cold_storage->tgl_update_spv = now();
    $cold_storage->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('cold_storage.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $cold_storage = Cold_storage::where('uuid', $uuid)->firstOrFail();
    $cold_storage->delete();

    return redirect()->route('cold_storage.index')
    ->with('success', 'Data Pemantauan Suhu Produk di Cold Storage berhasil dihapus');
}
}

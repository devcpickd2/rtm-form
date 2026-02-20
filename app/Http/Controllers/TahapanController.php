<?php

namespace App\Http\Controllers;

use App\Models\Tahapan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TahapanController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Tahapan::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.tahapan.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.tahapan.create', compact('produks'));
    }

    public function store(Request $request)
    {
      $username = Auth::user()->username ?? 'User RTM';
      $nama_produksi = session()->has('selected_produksi')
      ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
      : 'Produksi RTM';

      $request->validate([
        'date' => 'required|date',
        'shift' => 'required|in:1,2,3',
        'nama_produk' => 'required|string|max:255',
        'kode_produksi' => 'required|string|max:255',
        'filling_mulai' => 'nullable|date_format:H:i',
        'filling_selesai' => 'nullable|date_format:H:i',
        'waktu_iqf' => 'nullable|date_format:H:i',
        'waktu_sealer' => 'nullable|date_format:H:i',
        'waktu_xray' => 'nullable|date_format:H:i',
        'waktu_sticker' => 'nullable|date_format:H:i',
        'waktu_shrink' => 'nullable|date_format:H:i',
        'waktu_packing' => 'nullable|date_format:H:i',
        'waktu_cs' => 'nullable|date_format:H:i',

        // Suhu Filling array
        'suhu_filling' => 'nullable|array',
        'suhu_filling.*.nama_bahan' => 'nullable|string|max:255',
        'suhu_filling.*.suhu' => 'nullable|numeric',

        // Suhu lain: masing-masing array 6 elemen
        'suhu_masuk_iqf' => 'nullable|array',
        'suhu_masuk_iqf.*' => 'nullable|numeric',
        'suhu_keluar_iqf' => 'nullable|array',
        'suhu_keluar_iqf.*' => 'nullable|numeric',
        'suhu_sealer' => 'nullable|array',
        'suhu_sealer.*' => 'nullable|numeric',
        'suhu_xray' => 'nullable|array',
        'suhu_xray.*' => 'nullable|numeric',
        'suhu_sticker' => 'nullable|array',
        'suhu_sticker.*' => 'nullable|numeric',
        'suhu_shrink' => 'nullable|array',
        'suhu_shrink.*' => 'nullable|numeric',

        'downtime' => 'nullable|numeric',
        'suhu_cs' => 'nullable|numeric',
        'catatan' => 'nullable|string',
    ]);

      $data = $request->only([
        'date','shift','nama_produk','kode_produksi','filling_mulai','filling_selesai',
        'waktu_iqf','waktu_sealer','waktu_xray','waktu_sticker','waktu_shrink',
        'waktu_packing','waktu_cs','downtime','suhu_cs','catatan'
    ]);

      $data['username'] = $username;
      $data['nama_produksi'] = $nama_produksi;
      $data['status_produksi'] = "1";
      $data['status_spv'] = "0";

    // Filter suhu_filling: hanya baris yang ada nama_bahan atau suhu
      $suhu_filling = $request->input('suhu_filling', []);
      $filtered = [];
      foreach ($suhu_filling as $item) {
        if (!empty($item['nama_bahan']) || !empty($item['suhu'])) {
            $filtered[] = $item;
        }
    }

    $data['suhu_filling'] = json_encode($filtered, JSON_UNESCAPED_UNICODE);

    // Simpan suhu lainnya sebagai JSON agar fleksibel
    $suhuFields = ['suhu_masuk_iqf','suhu_keluar_iqf','suhu_sealer','suhu_xray','suhu_sticker','suhu_shrink'];
    foreach ($suhuFields as $field) {
        $data[$field] = json_encode($request->input($field, []), JSON_UNESCAPED_UNICODE);
    }

    $tahapan = Tahapan::create($data);

    $tahapan->update(['tgl_update_produksi' => Carbon::parse($tahapan->created_at)->addHour()]);

    return redirect()->route('tahapan.index')
    ->with('success', 'Data Pengecekan Suhu Produk berhasil disimpan.');
}

public function edit($uuid)
{
    $tahapan = Tahapan::where('uuid', $uuid)->firstOrFail();
    $produks = Produk::all();

    // Decode JSON suhu_filling dan suhu lainnya
    $suhu_fillingData = json_decode($tahapan->suhu_filling ?? '[]', true);

    $suhuFields = ['suhu_masuk_iqf','suhu_keluar_iqf','suhu_sealer','suhu_xray','suhu_sticker','suhu_shrink'];
    $suhuData = [];
    foreach ($suhuFields as $field) {
        $suhuData[$field] = json_decode($tahapan->$field ?? '[]', true);
    }

    return view('form.tahapan.edit', compact('tahapan', 'produks', 'suhu_fillingData', 'suhuData'));
}

public function update(Request $request, $uuid)
{
    $tahapan = Tahapan::where('uuid', $uuid)->firstOrFail();
    $username_updated = Auth::user()->username ?? 'User RTM';
    $nama_produksi = session()->has('selected_produksi')
    ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
    : 'Produksi RTM';

    $validated = $request->validate([
        'date' => 'required|date',
        'shift' => 'required|in:1,2,3',
        'nama_produk' => 'required|string|max:255',
        'kode_produksi' => 'required|string|max:255',
        'filling_mulai' => 'nullable|date_format:H:i',
        'filling_selesai' => 'nullable|date_format:H:i',
        'waktu_iqf' => 'nullable|date_format:H:i',
        'waktu_sealer' => 'nullable|date_format:H:i',
        'waktu_xray' => 'nullable|date_format:H:i',
        'waktu_sticker' => 'nullable|date_format:H:i',
        'waktu_shrink' => 'nullable|date_format:H:i',
        'waktu_packing' => 'nullable|date_format:H:i',
        'waktu_cs' => 'nullable|date_format:H:i',

        'suhu_filling' => 'nullable|array',
        'suhu_filling.*.nama_bahan' => 'nullable|string|max:255',
        'suhu_filling.*.suhu' => 'nullable|numeric',

        'suhu_masuk_iqf' => 'nullable|array',
        'suhu_masuk_iqf.*' => 'nullable|numeric',
        'suhu_keluar_iqf' => 'nullable|array',
        'suhu_keluar_iqf.*' => 'nullable|numeric',
        'suhu_sealer' => 'nullable|array',
        'suhu_sealer.*' => 'nullable|numeric',
        'suhu_xray' => 'nullable|array',
        'suhu_xray.*' => 'nullable|numeric',
        'suhu_sticker' => 'nullable|array',
        'suhu_sticker.*' => 'nullable|numeric',
        'suhu_shrink' => 'nullable|array',
        'suhu_shrink.*' => 'nullable|numeric',

        'downtime' => 'nullable|numeric',
        'suhu_cs' => 'nullable|numeric',
        'catatan' => 'nullable|string',
    ]);

    // Filter suhu_filling
    $suhu_filling = $validated['suhu_filling'] ?? [];
    $filtered = [];
    foreach ($suhu_filling as $item) {
        if (!empty($item['nama_bahan']) || !empty($item['suhu'])) {
            $filtered[] = $item;
        }
    }
    $validated['suhu_filling'] = json_encode($filtered, JSON_UNESCAPED_UNICODE);

    // Simpan suhu lainnya sebagai JSON
    $suhuFields = ['suhu_masuk_iqf','suhu_keluar_iqf','suhu_sealer','suhu_xray','suhu_sticker','suhu_shrink'];
    foreach ($suhuFields as $field) {
        $validated[$field] = json_encode($request->input($field, []), JSON_UNESCAPED_UNICODE);
    }

    $validated['username_updated'] = $username_updated;
    $validated['nama_produksi']    = $nama_produksi;

    $tahapan->update($validated);

    $tahapan->update(['tgl_update_produksi' => Carbon::parse($tahapan->updated_at)->addHour()]);

    return redirect()->route('tahapan.index')
    ->with('success', 'Data Pengecekan Suhu Produk berhasil diupdate.');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Tahapan::query()
    ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        });
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    return view('form.tahapan.verification', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $tahapan = Tahapan::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $tahapan->status_spv = $request->status_spv;
    $tahapan->catatan_spv = $request->catatan_spv;
    $tahapan->nama_spv = Auth::user()->username;
    $tahapan->tgl_update_spv = now();
    $tahapan->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('tahapan.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $tahapan = Tahapan::where('uuid', $uuid)->firstOrFail();
    $tahapan->delete();
    return redirect()->route('tahapan.verification')->with('success', 'Tahapan berhasil dihapus');
}

public function recyclebin()
{
    $tahapan = Tahapan::onlyTrashed()
    ->orderBy('deleted_at', 'desc')
    ->paginate(10);

    return view('form.tahapan.recyclebin', compact('tahapan'));
}
public function restore($uuid)
{
    $tahapan = Tahapan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $tahapan->restore();

    return redirect()->route('tahapan.recyclebin')
    ->with('success', 'Data berhasil direstore.');
}
public function deletePermanent($uuid)
{
    $tahapan = Tahapan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
    $tahapan->forceDelete();

    return redirect()->route('tahapan.recyclebin')
    ->with('success', 'Data berhasil dihapus permanen.');
}
}

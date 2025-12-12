<?php

namespace App\Http\Controllers;

use App\Models\Noodle;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NoodleController extends Controller{

   public function index(Request $request)
   {
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Noodle::query()
    ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('nama_produk', 'like', "%{$search}%")
        ->orWhere('mixing', 'like', "%{$search}%");
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    // ✅ decode kolom mixing supaya Blade gak ribet
    foreach ($data as $item) {
        $decoded = [];

        // Kalau field masih string JSON → decode
        if (is_string($item->mixing)) {
            $decoded = json_decode($item->mixing, true);
        } elseif (is_array($item->mixing)) {
            // Kalau sudah array → langsung pakai
            $decoded = $item->mixing;
        }

        // Pastikan array supaya Blade gak error
        $item->mixing_decoded = is_array($decoded) ? $decoded : [];
    }

    // ✅ return ke view
    return view('form.noodle.index', [
        'data'       => $data,
        'search'     => $search,
        'date' => $date,
    ]);
}

public function create()
{
    $produks = Produk::all();
    return view('form.noodle.create', compact('produks'));
}

public function store(Request $request)
{
    $data = $request->validate([
        'date'        => 'required|date',
        'shift'       => 'required',
        'nama_produk' => 'required',
        'catatan'     => 'nullable|string',

        'mixing'      => 'nullable|array',
        'mixing.*.nama_produk'   => 'nullable|string',
        'mixing.*.kode_produksi' => 'nullable|string',
        'mixing.*.bahan_utama'   => 'nullable|string',
        'mixing.*.kode_bahan'    => 'nullable|string',
        'mixing.*.berat_bahan'   => 'nullable|string',

        // array bahan lain
        'mixing.*.bahan_lain'     => 'nullable|array',
        'mixing.*.waktu_proses'   => 'nullable|array',
        'mixing.*.vacuum'         => 'nullable|array',
        'mixing.*.suhu_adonan'    => 'nullable|array',

        // array aging
        'mixing.*.waktu_aging'     => 'nullable|array',
        'mixing.*.rh_aging'        => 'nullable|array',
        'mixing.*.suhu_ruang_aging'=> 'nullable|array',

        // rolling
        'mixing.*.tebal_rolling'        => 'nullable|array',
        // cutting
        'mixing.*.sampling_cutiing'     => 'nullable|array',
        // boiling
        'mixing.*.suhu_setting_boiling' => 'nullable|string',
        'mixing.*.suhu_actual_boiling'  => 'nullable|array',
        'mixing.*.waktu_boiling'        => 'nullable|string',
        // washing
        'mixing.*.suhu_setting_washing' => 'nullable|string',
        'mixing.*.suhu_actual_washing'  => 'nullable|array',
        'mixing.*.waktu_washing'        => 'nullable|string',
        // cooling shock
        'mixing.*.suhu_setting_cooling' => 'nullable|string',
        'mixing.*.suhu_actual_cooling'  => 'nullable|array',
        'mixing.*.waktu_cooling'        => 'nullable|string',

        // lama proses
        'mixing.*.mulai'         => 'nullable|string',
        'mixing.*.selesai'       => 'nullable|string',

        // sensori
        'mixing.*.suhu_akhir'   => 'nullable|array',
        'mixing.*.suhu_after'   => 'nullable|array',
        'mixing.*.rasa'         => 'nullable|array',
        'mixing.*.kekenyalan'   => 'nullable|array',
        'mixing.*.warna'        => 'nullable|array',
    ]);

    // Filter null level atas
    $data = array_filter($data, fn($v) => !is_null($v));

    // Filter mixing & bahan_lain
    if (!empty($data['mixing']) && is_array($data['mixing'])) {
        $filteredMixing = [];

        foreach ($data['mixing'] as $mix) {
            // filter bahan_lain spesifik
            if (!empty($mix['bahan_lain']) && is_array($mix['bahan_lain'])) {
                $filteredBahanLain = [];
                foreach ($mix['bahan_lain'] as $bl) {
                    if (
                        !empty($bl['nama_bahan']) ||
                        !empty($bl['kode_bahan_lain']) ||
                        !empty($bl['berat_bahan'])
                    ) {
                        $filteredBahanLain[] = $bl;
                    }
                }
                $mix['bahan_lain'] = $filteredBahanLain;
            }

            // filter null/array kosong lain
            $clean = array_filter($mix, function ($v) {
                if (is_array($v)) {
                    return !empty(array_filter($v));
                }
                return !is_null($v) && $v !== '';
            });

            if (!empty($clean)) {
                $filteredMixing[] = $clean;
            }
        }

        $data['mixing'] = $filteredMixing;
    }

    $username = Auth::user()->username;
    $nama_produksi = session()->has('selected_produksi')
    ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
    : null;

    $data['username'] = $username;
    $data['nama_produksi'] = $nama_produksi;
    $data['status_produksi'] = "1";
    $data['status_spv']      = "0";

    $noodle = Noodle::create($data);
    $noodle->update(['tgl_update_produksi' => Carbon::parse($noodle->created_at)->addHour()]);

    return redirect()->route('noodle.index')
    ->with('success', 'Data Pemeriksaan Pemasakan Noodle berhasil disimpan');
}


public function edit($uuid)
{
    $noodle = Noodle::where('uuid', $uuid)->firstOrFail();
    $produks = Produk::all();

    if (is_array($noodle->mixing)) {
        $mixing = $noodle->mixing;
    } else {
        $mixing = json_decode($noodle->mixing, true) ?? [];
    }

    return view('form.noodle.edit', compact('noodle', 'produks', 'mixing'));
}

public function update(Request $request, $uuid)
{
    $noodle = Noodle::where('uuid', $uuid)->firstOrFail();
    
    $data = $request->validate([
        'date'        => 'required|date',
        'shift'       => 'required',
        'nama_produk' => 'required',
        'catatan'     => 'nullable|string',

        'mixing'      => 'nullable|array',
        'mixing.*.nama_produk'   => 'nullable|string',
        'mixing.*.kode_produksi' => 'nullable|string',
        'mixing.*.bahan_utama'   => 'nullable|string',
        'mixing.*.kode_bahan'    => 'nullable|string',
        'mixing.*.berat_bahan'   => 'nullable|string',

        'mixing.*.bahan_lain'     => 'nullable|array',
        'mixing.*.waktu_proses'   => 'nullable|array',
        'mixing.*.vacuum'         => 'nullable|array',
        'mixing.*.suhu_adonan'    => 'nullable|array',
        'mixing.*.waktu_aging'     => 'nullable|array',
        'mixing.*.rh_aging'        => 'nullable|array',
        'mixing.*.suhu_ruang_aging'=> 'nullable|array',

        'mixing.*.tebal_rolling'        => 'nullable|array',
        'mixing.*.sampling_cutiing'     => 'nullable|array',

        'mixing.*.suhu_setting_boiling' => 'nullable|string',
        'mixing.*.suhu_actual_boiling'  => 'nullable|array',
        'mixing.*.waktu_boiling'        => 'nullable|string',

        'mixing.*.suhu_setting_washing' => 'nullable|string',
        'mixing.*.suhu_actual_washing'  => 'nullable|array',
        'mixing.*.waktu_washing'        => 'nullable|string',

        'mixing.*.suhu_setting_cooling' => 'nullable|string',
        'mixing.*.suhu_actual_cooling'  => 'nullable|array',
        'mixing.*.waktu_cooling'        => 'nullable|string',

        'mixing.*.mulai'         => 'nullable|string',
        'mixing.*.selesai'       => 'nullable|string',

        'mixing.*.suhu_akhir'   => 'nullable|array',
        'mixing.*.suhu_after'   => 'nullable|array',
        'mixing.*.rasa'         => 'nullable|array',
        'mixing.*.kekenyalan'   => 'nullable|array',
        'mixing.*.warna'        => 'nullable|array',
    ]);

    // Filter null level atas
    $data = array_filter($data, fn($v) => !is_null($v));

    // Filter mixing & bahan_lain
    if (!empty($data['mixing']) && is_array($data['mixing'])) {
        $filteredMixing = [];

        foreach ($data['mixing'] as $mix) {
            // filter bahan_lain spesifik
            if (!empty($mix['bahan_lain']) && is_array($mix['bahan_lain'])) {
                $filteredBahanLain = [];
                foreach ($mix['bahan_lain'] as $bl) {
                    if (
                        !empty($bl['nama_bahan']) ||
                        !empty($bl['kode_bahan_lain']) ||
                        !empty($bl['berat_bahan'])
                    ) {
                        $filteredBahanLain[] = $bl;
                    }
                }
                $mix['bahan_lain'] = $filteredBahanLain;
            }

            // filter null/array kosong lain
            $clean = array_filter($mix, function ($v) {
                if (is_array($v)) {
                    return !empty(array_filter($v));
                }
                return !is_null($v) && $v !== '';
            });

            if (!empty($clean)) {
                $filteredMixing[] = $clean;
            }
        }

        $data['mixing'] = $filteredMixing;
    }

    // ambil username_updated & nama_produksi
    $username_updated = Auth::user()->username;
    $nama_produksi = session()->has('selected_produksi')
    ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
    : null;

    $data['username_updated'] = $username_updated;
    $data['nama_produksi'] = $nama_produksi;

    $noodle->update($data);

    $noodle->update(['tgl_update_produksi' => Carbon::parse($noodle->updated_at)->addHour()]);

    return redirect()->route('noodle.index')
    ->with('success', 'Data Pemeriksaan Pemasakan noodle berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Noodle::query()
    ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('nama_produk', 'like', "%{$search}%")
        ->orWhere('mixing', 'like', "%{$search}%");
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    // ✅ decode kolom mixing supaya Blade gak ribet
    foreach ($data as $item) {
        $decoded = [];

        // Kalau field masih string JSON → decode
        if (is_string($item->mixing)) {
            $decoded = json_decode($item->mixing, true);
        } elseif (is_array($item->mixing)) {
            // Kalau sudah array → langsung pakai
            $decoded = $item->mixing;
        }

        // Pastikan array supaya Blade gak error
        $item->mixing_decoded = is_array($decoded) ? $decoded : [];
    }

    // ✅ return ke view
    return view('form.noodle.verification', [
        'data'       => $data,
        'search'     => $search,
        'date' => $date,
    ]);
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $noodle = Noodle::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $noodle->status_spv = $request->status_spv;
    $noodle->catatan_spv = $request->catatan_spv;
    $noodle->nama_spv = Auth::user()->username;
    $noodle->tgl_update_spv = now();
    $noodle->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('noodle.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $noodle = Noodle::where('uuid', $uuid)->firstOrFail();
    $noodle->delete();

    return redirect()->route('noodle.index')->with('success', 'Data Pemeriksaan Pemasakan Noodle berhasil dihapus');
}
}

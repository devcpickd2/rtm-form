<?php

namespace App\Http\Controllers;

use App\Models\Thumbling;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ThumblingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Thumbling::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.thumbling.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.thumbling.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->value('name')
        : 'Produksi RTM';

        $validated = $request->validate([
        // Data umum
            'date' => 'required|date',
            'shift' => 'required|string',
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'identifikasi_daging' => 'required|string',
            'asal_daging' => 'required|string',
            'kode_daging' => 'nullable|array',
            'berat_daging' => 'nullable|array',
            'suhu_daging' => 'nullable|array',
            'rata_daging' => 'nullable|array',
            'kondisi_daging' => 'nullable|string',
        // Bahan Lain
            'bahan_lain' => 'nullable|array',
            'bahan_lain.*.premix' => 'nullable|string',
            'bahan_lain.*.kode' => 'nullable|string',
            'bahan_lain.*.berat' => 'nullable|numeric',
            'bahan_lain.*.sens' => 'nullable|string',
        // Parameter cairan
            'air' => 'nullable|string',
            'suhu_air' => 'nullable|numeric',
            'suhu_marinade' => 'nullable|numeric',
            'lama_pengadukan' => 'nullable|numeric',
            'marinade_brix_salinity' => 'nullable|string',
        // Parameter thumbling
            'drum_on' => 'nullable|numeric',
            'drum_off' => 'nullable|numeric',
            'drum_speed' => 'nullable|numeric',
            'vacuum_time' => 'nullable|numeric',
            'total_time' => 'nullable|numeric',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'suhu_daging_thumbling' => 'nullable|array',
            'rata_daging_thumbling' => 'nullable|numeric',
            'kondisi_daging_akhir' => 'nullable|string',
            'catatan_akhir' => 'nullable|string',
        ]);

        $validated['username'] = $username;
        $validated['nama_produksi'] = $nama_produksi;
        $validated['status_produksi'] = "1";
        $validated['status_spv'] = "0";
        $validated['tgl_update_produksi'] = now()->addHour();

        Thumbling::create($validated);

        return redirect()->route('thumbling.index')->with('success', 'Data Pemeriksaan Proses Thumbling berhasil disimpan');
    }

    public function edit($uuid)
    {
        $thumbling = Thumbling::where('uuid', $uuid)->firstOrFail();
        $produks   = Produk::all();

        return view('form.thumbling.edit', compact('thumbling', 'produks'));
    }
    public function update(Request $request, $uuid)
    {
        $thumbling = Thumbling::where('uuid', $uuid)->firstOrFail();

        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->value('name')
        : 'Produksi RTM';

        $validated = $request->validate([
        // Data umum
            'date' => 'required|date',
            'shift' => 'required|string',
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'identifikasi_daging' => 'required|string',
            'asal_daging' => 'required|string',
            'kode_daging' => 'nullable|array',
            'berat_daging' => 'nullable|array',
            'suhu_daging' => 'nullable|array',
            'rata_daging' => 'nullable|array',
            'kondisi_daging' => 'nullable|string',
        // Bahan Lain
            'bahan_lain' => 'nullable|array',
            'bahan_lain.*.premix' => 'nullable|string',
            'bahan_lain.*.kode' => 'nullable|string',
            'bahan_lain.*.berat' => 'nullable|numeric',
            'bahan_lain.*.sens' => 'nullable|string',
        // Parameter cairan
            'air' => 'nullable|string',
            'suhu_air' => 'nullable|numeric',
            'suhu_marinade' => 'nullable|numeric',
            'lama_pengadukan' => 'nullable|numeric',
            'marinade_brix_salinity' => 'nullable|string',
        // Parameter thumbling
            'drum_on' => 'nullable|numeric',
            'drum_off' => 'nullable|numeric',
            'drum_speed' => 'nullable|numeric',
            'vacuum_time' => 'nullable|numeric',
            'total_time' => 'nullable|numeric',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'suhu_daging_thumbling' => 'nullable|array',
            'rata_daging_thumbling' => 'nullable|numeric',
            'kondisi_daging_akhir' => 'nullable|string',
            'catatan_akhir' => 'nullable|string',
        ]);

    // Tambahkan info tambahan
        $validated['username_updated'] = $username_updated;
        $validated['nama_produksi'] = $nama_produksi;
        $validated['tgl_update_produksi'] = now()->addHour();

    // Update seluruh data
        $thumbling->update($validated);

        return redirect()->route('thumbling.index')->with('success', 'Data Pemeriksaan Proses Thumbling berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Thumbling::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.thumbling.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $thumbling = Thumbling::where('uuid', $uuid)->firstOrFail();

        $thumbling->status_spv = $request->status_spv;
        $thumbling->catatan_spv = $request->catatan_spv;
        $thumbling->nama_spv = Auth::user()->username;
        $thumbling->tgl_update_spv = now();
        $thumbling->save();

        return redirect()->route('thumbling.verification')->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $thumbling = Thumbling::where('uuid', $uuid)->firstOrFail();
        $thumbling->delete();

        return redirect()->route('thumbling.index')->with('success', 'Data Pemeriksaan Proses Thumbling berhasil dihapus');
    }
}

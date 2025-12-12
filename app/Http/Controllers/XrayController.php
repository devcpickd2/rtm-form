<?php

namespace App\Http\Controllers;

use App\Models\Xray;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class XrayController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Xray::query()
        ->when($search, fn($q) => $q->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%"))
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.xray.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.xray.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'no_program' => 'required',
            'catatan'     => 'nullable|string',
            'pemeriksaan' => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'kode_produksi', 'no_program', 'catatan']);
        $data['username']        = $username;
        $data['nama_produksi']   = $nama_produksi;
        $data['status_produksi'] = "1";
        $data['status_spv']      = "0";
        $data['pemeriksaan']     = json_encode($request->input('pemeriksaan', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $xray = Xray::create($data);
        $xray->update(['tgl_update_produksi' => Carbon::parse($xray->created_at)->addHour()]);

        return redirect()->route('xray.index')->with('success', 'Data Pemeriksaan X RAY berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $xray = Xray::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();

    // Decode JSON sampai benar-benar array
        $pemeriksaanData = $xray->pemeriksaan ?? '[]';
        while (is_string($pemeriksaanData)) {
            $decoded = json_decode($pemeriksaanData, true);
            if ($decoded === null) break; 
            $pemeriksaanData = $decoded;
        }
        if (!is_array($pemeriksaanData)) $pemeriksaanData = [];

        return view('form.xray.edit', compact('xray', 'produks', 'pemeriksaanData'));
    }

    public function update(Request $request, string $uuid)
    {
        $xray = Xray::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'no_program' => 'required',
            'catatan'     => 'nullable|string',
            'pemeriksaan' => 'nullable|array',
        ]);

    // Normalisasi data pemeriksaan
        $pemeriksaan = [];
        foreach ($request->pemeriksaan ?? [] as $item) {
            $pemeriksaan[] = [
                'pukul' => $item['pukul'] ?? null,
                'glass_ball' => $item['glass_ball'] ?? null,
                'glass_ball_status' => $item['glass_ball_status'] ?? 'Tidak Oke',
                'ceramic' => $item['ceramic'] ?? null,
                'ceramic_status' => $item['ceramic_status'] ?? 'Tidak Oke',
                'sus_wire' => $item['sus_wire'] ?? null,
                'sus_wire_status' => $item['sus_wire_status'] ?? 'Tidak Oke',
                'sus_ball' => $item['sus_ball'] ?? null,
                'sus_ball_status' => $item['sus_ball_status'] ?? 'Tidak Oke',
                'keterangan' => $item['keterangan'] ?? null,
                'tindakan_koreksi' => $item['tindakan_koreksi'] ?? null,
            ];
        }

        $xray->update([
            'date' => $request->date,
            'shift' => $request->shift,
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'no_program' => $request->no_program,
            'catatan' => $request->catatan,
            'username_updated' => $username_updated,
            'nama_produksi' => $nama_produksi,
            'pemeriksaan' => json_encode($pemeriksaan, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);

    // Update jam update produksi
        $xray->update(['tgl_update_produksi' => now()->addHour()]);

        return redirect()->route('xray.index')->with('success', 'Data Pemeriksaan X RAY berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Xray::query()
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

        return view('form.xray.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $xray = Xray::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $xray->status_spv = $request->status_spv;
        $xray->catatan_spv = $request->catatan_spv;
        $xray->nama_spv = Auth::user()->username;
        $xray->tgl_update_spv = now();
        $xray->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('xray.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }


    public function destroy(string $uuid)
    {
        $xray = Xray::where('uuid', $uuid)->firstOrFail();
        $xray->delete();

        return redirect()->route('xray.index')->with('success', 'Data Pemeriksaan X RAY berhasil dihapus');
    }
}

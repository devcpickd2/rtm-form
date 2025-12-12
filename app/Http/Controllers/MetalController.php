<?php

namespace App\Http\Controllers;

use App\Models\Metal;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MetalController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Metal::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('username_updated', 'like', "%{$search}%")
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

        return view('form.metal.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.metal.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'no_program'  => 'required',
            'catatan'     => 'nullable|string',
            'pemeriksaan' => 'nullable|array',
        ]);

        // ambil username & nama_produksi
        $username = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        // Konversi pemeriksaan ke JSON
        $pemeriksaan = json_encode($request->input('pemeriksaan', []), JSON_UNESCAPED_UNICODE);

        $metal = Metal::create([
            'date'           => $request->date,
            'shift'          => $request->shift,
            'nama_produk'    => $request->nama_produk,
            'kode_produksi'  => $request->kode_produksi,
            'no_program'     => $request->no_program,
            'catatan'        => $request->catatan,
            'username'       => $username,
            'nama_produksi'  => $nama_produksi,
            'status_produksi'=> "1",
            'status_spv'     => "0",
            'pemeriksaan'    => $pemeriksaan,
        ]);

        // Set tgl_update_produksi = created_at + 1 jam
        $metal->update(['tgl_update_produksi' => Carbon::parse($metal->created_at)->addHour()]);

        return redirect()->route('metal.index')
        ->with('success', 'Data Pemeriksaan X RAY berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();

        // Decode JSON menjadi array
        $pemeriksaanData = !empty($metal->pemeriksaan)
        ? json_decode($metal->pemeriksaan, true)
        : [];

        return view('form.metal.edit', compact('metal', 'produks', 'pemeriksaanData'));
    }

    public function update(Request $request, string $uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'no_program'  => 'required',
            'catatan'     => 'nullable|string',
            'pemeriksaan' => 'nullable|array',
        ]);

        // proses pemeriksaan
        $pemeriksaan = [];
        if ($request->has('pemeriksaan')) {
            foreach ($request->pemeriksaan as $item) {
                $pemeriksaan[] = [
                    'pukul'            => $item['pukul'] ?? null,
                    'fe'               => $item['fe'] ?? 'Tidak Oke',
                    'non_fe'           => $item['non_fe'] ?? 'Tidak Oke',
                    'sus_316'          => $item['sus_316'] ?? 'Tidak Oke',
                    'keterangan'       => $item['keterangan'] ?? null,
                    'tindakan_koreksi' => $item['tindakan_koreksi'] ?? null,
                ];
            }
        }

        // ambil username_updated & nama_produksi
        $username_updated = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        // update data
        $metal->update([
            'date'              => $request->date,
            'shift'             => $request->shift,
            'nama_produk'       => $request->nama_produk,
            'kode_produksi'     => $request->kode_produksi,
            'no_program'        => $request->no_program,
            'catatan'           => $request->catatan,
            'username_updated'  => $username_updated,
            'nama_produksi'     => $nama_produksi,
            'pemeriksaan'       => json_encode($pemeriksaan, JSON_UNESCAPED_UNICODE),
        ]);

        // Update tgl_update_produksi = updated_at + 1 jam
        $metal->update(['tgl_update_produksi' => Carbon::parse($metal->updated_at)->addHour()]);

        return redirect()->route('metal.index')->with('success', 'Data Pemeriksaan X RAY berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Metal::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('username_updated', 'like', "%{$search}%")
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

        return view('form.metal.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $metal = Metal::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $metal->status_spv = $request->status_spv;
        $metal->catatan_spv = $request->catatan_spv;
        $metal->nama_spv = Auth::user()->username;
        $metal->tgl_update_spv = now();
        $metal->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('metal.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();
        $metal->delete();

        return redirect()->route('metal.index')
        ->with('success', 'Data Pemeriksaan X RAY berhasil dihapus');
    }
}

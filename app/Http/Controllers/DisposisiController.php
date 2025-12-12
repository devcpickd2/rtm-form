<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DisposisiController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Disposisi::query()
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

        return view('form.disposisi.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.disposisi.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'jumlah' => 'nullable|numeric',
            'ketidaksesuaian' => 'nullable|string',
            'tindakan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift',
            'nama_produk', 'kode_produksi', 'jumlah', 'ketidaksesuaian', 'tindakan',
            'keterangan', 'catatan'
        ]);

        $data['username'] = Auth::user()->username;

        $data['nama_produksi'] = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        $disposisi = Disposisi::create($data);

        // Set tgl_update_produksi = created_at + 1 jam
        $disposisi->update(['tgl_update_produksi' => Carbon::parse($disposisi->created_at)->addHour()]);

        return redirect()->route('disposisi.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $produks = Produk::all();
        $disposisi = Disposisi::where('uuid', $uuid)->firstOrFail();
        return view('form.disposisi.edit', compact('disposisi', 'produks'));
    }

    public function update(Request $request, string $uuid)
    {
        $disposisi = Disposisi::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'  => 'required|date',
            'shift' => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',
            'jumlah' => 'nullable|numeric',
            'ketidaksesuaian' => 'nullable|string',
            'tindakan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift',
            'nama_produk', 'kode_produksi', 'jumlah', 'ketidaksesuaian', 'tindakan',
            'keterangan', 'catatan'
        ]);

        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        $disposisi->update($data);

        // Update tgl_update_produksi = updated_at + 1 jam
        $disposisi->update(['tgl_update_produksi' => Carbon::parse($disposisi->updated_at)->addHour()]);

        return redirect()->route('disposisi.index')->with('success', 'Data berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Disposisi::query()
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

        return view('form.disposisi.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $disposisi = Disposisi::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $disposisi->status_spv = $request->status_spv;
        $disposisi->catatan_spv = $request->catatan_spv;
        $disposisi->nama_spv = Auth::user()->username;
        $disposisi->tgl_update_spv = now();
        $disposisi->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('disposisi.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }


    public function destroy($uuid)
    {
        $disposisi = Disposisi::where('uuid', $uuid)->firstOrFail();
        $disposisi->delete();

        return redirect()->route('disposisi.index')->with('success', 'Data berhasil dihapus');
    }
}

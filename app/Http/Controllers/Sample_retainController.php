<?php

namespace App\Http\Controllers;

use App\Models\Sample_retain;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Sample_retainController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $end_date   = $request->input('end_date');

        $data = Sample_retain::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.sample_retain.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.sample_retain.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

        $request->validate([
            'nama_produk'    => 'required',
            'kode_produksi'  => 'required',
            'analisa'        => 'nullable|array',
        ]);

        $data = $request->only(['nama_produk', 'kode_produksi']);
        $data['username']         = $username;
        $data['status_spv']       = "0";

        $data['analisa'] = json_encode($request->input('analisa', []), JSON_UNESCAPED_UNICODE);

        Sample_retain::create($data);

        return redirect()->route('sample_retain.index')
        ->with('success', 'Data Pemeriksaan Sample Retain berhasil disimpan');
    }

    public function edit($uuid)
    {
        $sample_retain = Sample_retain::where('uuid', $uuid)->firstOrFail();

    // Pastikan analisa di-decode
        $sample_retain->analisa = json_decode($sample_retain->analisa, true) ?? [];

        $produks = Produk::all();

        return view('form.sample_retain.edit', compact('sample_retain', 'produks'));
    }

    public function update(Request $request, string $uuid)
    {
        $sample_retain = Sample_retain::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username;

        $request->validate([
            'nama_produk'    => 'required',
            'kode_produksi'  => 'required',
            'analisa'        => 'nullable|array',
        ]);

        $analisa = $request->input('analisa', []);

        $data = [
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'username_updated' => $username_updated,
            'analisa' => json_encode($analisa, JSON_UNESCAPED_UNICODE),
        ];

        $sample_retain->update($data);

        return redirect()->route('sample_retain.index')->with('success', 'Data Pemeriksaan Sample Retain berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Sample_retain::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.sample_retain.verification', compact('data', 'search', 'date', 'end_date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $sample_retain = Sample_retain::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $sample_retain->status_spv = $request->status_spv;
        $sample_retain->catatan_spv = $request->catatan_spv;
        $sample_retain->nama_spv = Auth::user()->username;
        $sample_retain->tgl_update_spv = now();
        $sample_retain->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('sample_retain.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $sample_retain = Sample_retain::where('uuid', $uuid)->firstOrFail();
        $sample_retain->delete();

        return redirect()->route('sample_retain.index')
        ->with('success', 'Data Pemeriksaan Sample Retain berhasil dihapus');
    }
}

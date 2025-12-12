<?php

namespace App\Http\Controllers;

use App\Models\Sample_bulanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Sample_bulananController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Sample_bulanan::query() 
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('plant', 'like', "%{$search}%")
            ->orWhere('sample_bulan', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.sample_bulanan.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.sample_bulanan.create', compact('produks'));
    }

    public function store(Request $request)
    {
     $username = Auth::user()->username;

     $request->validate([
        'date'           => 'required|date',
        'sample_bulan'   => 'required',
        'plant'          => 'required',
        'sample_storage' => 'nullable|array',
        'sample'         => 'nullable|array',
        'catatan'        => 'nullable|string',
        'nama_warehouse' => 'nullable|string',
    ]);

     $data = $request->only(['date', 'sample_bulan', 'plant', 'catatan', 'nama_warehouse']);
     $data['username']         = $username;
     $data['status_warehouse'] = "1";
     $data['status_spv']       = "0";

        // Konversi sample ke JSON
     $data['sample_storage'] = json_encode($request->input('sample_storage', []), JSON_UNESCAPED_UNICODE);
     $data['sample'] = json_encode($request->input('sample', []), JSON_UNESCAPED_UNICODE);

     Sample_bulanan::create($data);

     return redirect()->route('sample_bulanan.index')
     ->with('success', 'Data Sample Bulanan RND berhasil disimpan');
 }

 public function edit(string $uuid)
 {
    $sample_bulanan = Sample_bulanan::where('uuid', $uuid)->firstOrFail();
    $produks = Produk::all();

    $sampleStorage = !empty($sample_bulanan->sample_storage) ? json_decode($sample_bulanan->sample_storage, true) : [];
    $sampleData = !empty($sample_bulanan->sample) ? json_decode($sample_bulanan->sample, true) : [];

    return view('form.sample_bulanan.edit', compact('sample_bulanan', 'produks', 'sampleData', 'sampleStorage'));
}

public function update(Request $request, string $uuid)
{
    $sample_bulanan = Sample_bulanan::where('uuid', $uuid)->firstOrFail();
    $username_updated = Auth::user()->username;

    $request->validate([
        'date'           => 'required|date',
        'sample_bulan'   => 'required|date',
        'plant'          => 'required',
        'sample_storage' => 'nullable|array',
        'sample'         => 'nullable|array',
        'catatan'        => 'nullable|string',
        'nama_warehouse' => 'nullable|string',
    ]);

    $sample_storage = $request->input('sample_storage', []);
    $sample = $request->input('sample', []);

    $data = [
        'date' => $request->date,
        'sample_bulan' => $request->sample_bulan,
        'plant' => $request->plant,
        'catatan' => $request->catatan,
        'nama_warehouse' => $request->nama_warehouse,
        'username_updated' => $username_updated,
        'sample_storage' => json_encode($sample_storage, JSON_UNESCAPED_UNICODE),
        'sample' => json_encode($sample, JSON_UNESCAPED_UNICODE),
    ];

    $sample_bulanan->update($data);

    return redirect()->route('sample_bulanan.index')->with('success', 'Data Sample Bulanan RND berhasil diperbarui');
}

public function verification(Request $request)
{
    $search     = $request->input('search');
    $date = $request->input('date');

    $data = Sample_bulanan::query() 
    ->when($search, function ($query) use ($search) {
        $query->where('username', 'like', "%{$search}%")
        ->orWhere('plant', 'like', "%{$search}%")
        ->orWhere('sample_bulan', 'like', "%{$search}%");
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10)
    ->appends($request->all());

    return view('form.sample_bulanan.verification', compact('data', 'search', 'date'));
}

public function updateVerification(Request $request, $uuid)
{
    // Validasi input
    $request->validate([
        'status_spv' => 'required|in:1,2',
        'catatan_spv' => 'nullable|string|max:255',
    ]);

    // Cari data berdasarkan UUID
    $sample_bulanan = Sample_bulanan::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
    $sample_bulanan->status_spv = $request->status_spv;
    $sample_bulanan->catatan_spv = $request->catatan_spv;
    $sample_bulanan->nama_spv = Auth::user()->username;
    $sample_bulanan->tgl_update_spv = now();
    $sample_bulanan->save();

    // Redirect kembali dengan pesan sukses
    return redirect()->route('sample_bulanan.verification')
    ->with('success', 'Status verifikasi berhasil diperbarui.');
}

public function destroy($uuid)
{
    $sample_bulanan = Sample_bulanan::where('uuid', $uuid)->firstOrFail();
    $sample_bulanan->delete();

    return redirect()->route('sample_bulanan.index')
    ->with('success', 'Data Sample Bulanan RND berhasil dihapus');
}
}

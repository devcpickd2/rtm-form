<?php

namespace App\Http\Controllers;

use App\Models\Rice;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiceController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Rice::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('cooker', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.rice.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.rice.create', compact('produks'));
    }

    public function store(Request $request)
    {
        // Ambil username & nama_produksi dari Auth/session
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'catatan'     => 'nullable|string',
            'cooker'      => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'catatan']);
        $data['username']        = $username;
        $data['nama_produksi']   = $nama_produksi;
        $data['status_produksi'] = "1";
        $data['status_spv']      = "0";
        $data['cooker']          = json_encode($request->input('cooker', []), JSON_UNESCAPED_UNICODE);

        $rice = Rice::create($data);

        // contoh jika mau pakai tgl_update_produksi
        $rice->update(['tgl_update_produksi' => Carbon::parse($rice->created_at)->addHour()]);

        return redirect()->route('rice.index')
        ->with('success', 'Data Pemeriksaan Pemasakan dengan Rice berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $data = Rice::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();
        $cookerData = !empty($data->cooker) ? json_decode($data->cooker, true) : [];

        return view('form.rice.edit', compact('data', 'produks', 'cookerData'));
    }

    public function update(Request $request, string $uuid)
    {
        $rice = Rice::where('uuid', $uuid)->firstOrFail();

        // Ambil username_updated & nama_produksi
        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'catatan'     => 'nullable|string',
            'cooker'      => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'catatan']);
        $data['username_updated'] = $username_updated;
        $data['nama_produksi']    = $nama_produksi;
        $data['cooker']           = json_encode($request->input('cooker', []), JSON_UNESCAPED_UNICODE);

        $rice->update($data);

        // update tgl_update_produksi = updated_at +1 jam
        $rice->update(['tgl_update_produksi' => Carbon::parse($rice->updated_at)->addHour()]);

        return redirect()->route('rice.index')
        ->with('success', 'Data Pemeriksaan Pemasakan dengan Rice Cooker berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Rice::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('cooker', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.rice.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $rice = Rice::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $rice->status_spv = $request->status_spv;
        $rice->catatan_spv = $request->catatan_spv;
        $rice->nama_spv = Auth::user()->username;
        $rice->tgl_update_spv = now();
        $rice->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('rice.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }


    public function destroy($uuid)
    {
        $rice = Rice::where('uuid', $uuid)->firstOrFail();
        $rice->delete();

        return redirect()->route('rice.index')
        ->with('success', 'Data Pemeriksaan Pemasakan dengan Rice Cooker berhasil dihapus');
    }
}

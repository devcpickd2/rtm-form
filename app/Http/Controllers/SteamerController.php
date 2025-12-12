<?php

namespace App\Http\Controllers;

use App\Models\Steamer;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SteamerController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Steamer::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('steaming', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.steamer.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.steamer.create', compact('produks'));
    }

    public function store(Request $request)
    {
        // Ambil username & nama_produksi
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'catatan'     => 'nullable|string',
            'steaming'    => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'catatan']);
        $data['username']        = $username;
        $data['nama_produksi']   = $nama_produksi;
        $data['status_produksi'] = "1";
        $data['status_spv']      = "0";
        $data['steaming'] = json_encode($request->input('steaming', []), JSON_UNESCAPED_UNICODE);

        $steamer = Steamer::create($data);

        // set tgl_update_produksi = created_at +1 jam
        $steamer->update(['tgl_update_produksi' => Carbon::parse($steamer->created_at)->addHour()]);

        return redirect()->route('steamer.index')
        ->with('success', 'Data Pemeriksaan Pemasakan dengan Steamer berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $data = Steamer::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();

        $steamingData = !empty($data->steaming) ? json_decode($data->steaming, true) : [];

        return view('form.steamer.edit', compact('data', 'produks', 'steamingData'));
    }

    public function update(Request $request, string $uuid)
    {
        $steamer = Steamer::where('uuid', $uuid)->firstOrFail();

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
            'steaming'    => 'nullable|array',
        ]);

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
            'nama_produksi'    => $nama_produksi,
            'steaming'         => json_encode($request->input('steaming', []), JSON_UNESCAPED_UNICODE),
        ];

        $steamer->update($data);

        // update tgl_update_produksi = updated_at +1 jam
        $steamer->update(['tgl_update_produksi' => Carbon::parse($steamer->updated_at)->addHour()]);

        return redirect()->route('steamer.index')
        ->with('success', 'Data Pemeriksaan Pemasakan dengan Steamer berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Steamer::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('steaming', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.steamer.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $steamer = Steamer::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $steamer->status_spv = $request->status_spv;
        $steamer->catatan_spv = $request->catatan_spv;
        $steamer->nama_spv = Auth::user()->username;
        $steamer->tgl_update_spv = now();
        $steamer->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('steamer.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $steamer = Steamer::where('uuid', $uuid)->firstOrFail();
        $steamer->delete();

        return redirect()->route('steamer.index')
        ->with('success', 'Data Pemeriksaan Pemasakan dengan Steamer berhasil dihapus');
    }
}

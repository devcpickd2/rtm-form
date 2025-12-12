<?php

namespace App\Http\Controllers;

use App\Models\Repack;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RepackController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Repack::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
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

        return view('form.repack.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.repack.create', compact('produks'));
    }

    public function store(Request $request)
    {
        // ambil username & nama_produksi dari session
        $username = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        // fungsi bersihkan string
        $cleanString = fn($str) => is_string($str) ? trim(preg_replace('/\s+/', ' ', $str)) : $str;

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required',
            'karton'        => 'nullable|string',
            'expired_date'  => 'nullable|date',
            'jumlah'        => 'nullable|integer',
            'kodefikasi'    => 'nullable|string',
            'content'       => 'nullable|string',
            'kerapihan'     => 'nullable|string',
            'lainnya'       => 'nullable|string',
            'keterangan'    => 'nullable|string',
            'catatan'       => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_produk', 'kode_produksi', 'karton',
            'expired_date', 'jumlah', 'kodefikasi', 'content', 'kerapihan', 'lainnya',
            'keterangan', 'catatan'
        ]);

        // bersihkan string
        $data['nama_produk']   = $cleanString($data['nama_produk']);
        $data['kode_produksi'] = $cleanString($data['kode_produksi']);

        $data['username']        = $username;
        $data['nama_produksi']   = $nama_produksi;
        $data['status_produksi'] = "1";
        $data['status_spv']      = "0";

        $repack = Repack::create($data);

        // set tgl_update_produksi = created_at + 1 jam
        $repack->update(['tgl_update_produksi' => Carbon::parse($repack->created_at)->addHour()]);

        return redirect()->route('repack.index')->with('success', 'Data Monitoring Proses Repack berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $produks = Produk::all();
        $repack  = Repack::where('uuid', $uuid)->firstOrFail();
        return view('form.repack.edit', compact('repack', 'produks'));
    }

    public function update(Request $request, string $uuid)
    {
        $repack = Repack::where('uuid', $uuid)->firstOrFail();

        // ambil username_updated & nama_produksi dari session
        $username_updated = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        // fungsi bersihkan string
        $cleanString = fn($str) => is_string($str) ? trim(preg_replace('/\s+/', ' ', $str)) : $str;

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required',
            'karton'        => 'nullable|string',
            'expired_date'  => 'nullable|date',
            'jumlah'        => 'nullable|integer',
            'kodefikasi'    => 'nullable|string',
            'content'       => 'nullable|string',
            'kerapihan'     => 'nullable|string',
            'lainnya'       => 'nullable|string',
            'keterangan'    => 'nullable|string',
            'catatan'       => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_produk', 'kode_produksi', 'karton',
            'expired_date', 'jumlah', 'kodefikasi', 'content', 'kerapihan', 'lainnya',
            'keterangan', 'catatan'
        ]);

        $data['nama_produk']   = $cleanString($data['nama_produk']);
        $data['kode_produksi'] = $cleanString($data['kode_produksi']);

        $data['username_updated'] = $username_updated;
        $data['nama_produksi']    = $nama_produksi;

        $repack->update($data);

        // update tgl_update_produksi = updated_at +1 jam
        $repack->update(['tgl_update_produksi' => Carbon::parse($repack->updated_at)->addHour()]);

        return redirect()->route('repack.index')->with('success', 'Data Monitoring Proses Repack berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Repack::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
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

        return view('form.repack.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $repack = Repack::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $repack->status_spv = $request->status_spv;
        $repack->catatan_spv = $request->catatan_spv;
        $repack->nama_spv = Auth::user()->username;
        $repack->tgl_update_spv = now();
        $repack->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('repack.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy(string $uuid)
    {
        $repack = Repack::where('uuid', $uuid)->firstOrFail();
        $repack->delete();

        return redirect()->route('repack.index')->with('success', 'Data Monitoring Proses Repack berhasil dihapus');
    }
}

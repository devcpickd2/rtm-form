<?php

namespace App\Http\Controllers;

use App\Models\Reject;
use App\Models\Metal;
use App\Models\Xray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RejectController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Reject::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('nama_mesin', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.reject.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
    // Ambil semua kode_produksi yang sudah ada di tabel reject
        $existingMetal = Reject::where('nama_mesin', 'Metal Detector')
        ->pluck('kode_produksi')
        ->toArray();

        $existingXray = Reject::where('nama_mesin', 'X-Ray')
        ->pluck('kode_produksi')
        ->toArray();

    // Ambil hanya produk yang belum pernah direject
        $metalProducts = Metal::select('nama_produk', 'kode_produksi')
        ->whereNotIn('kode_produksi', $existingMetal)
        ->orderBy('created_at', 'desc')
        ->get();

        $xrayProducts = Xray::select('nama_produk', 'kode_produksi')
        ->whereNotIn('kode_produksi', $existingXray)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('form.reject.create', compact('metalProducts', 'xrayProducts'));
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
            'date'                => 'required|date',
            'shift'               => 'required',
            'nama_produk'         => 'required',
            'kode_produksi'       => 'required',
            'nama_mesin'          => 'required',
            'jumlah_tidak_lolos'  => 'nullable|integer',
            'jumlah_kontaminan'   => 'nullable|integer',
            'jenis_kontaminan'    => 'nullable|string',
            'posisi_kontaminan'   => 'nullable|string',
            'false_rejection'     => 'nullable|string',
            'catatan'             => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_produk', 'kode_produksi', 'nama_mesin',
            'jumlah_tidak_lolos', 'jumlah_kontaminan', 'jenis_kontaminan',
            'posisi_kontaminan', 'false_rejection', 'catatan'
        ]);

        // bersihkan string
        $data['nama_produk']   = $cleanString($data['nama_produk']);
        $data['kode_produksi'] = $cleanString($data['kode_produksi']);

        $data['username']       = $username;
        $data['nama_produksi']  = $nama_produksi;
        $data['status_produksi'] = "1";
        $data['status_spv']      = "0";

        $reject = Reject::create($data);

        // set tgl_update_produksi = created_at + 1 jam
        $reject->update(['tgl_update_produksi' => Carbon::parse($reject->created_at)->addHour()]);

        return redirect()->route('reject.index')->with('success', 'Data Monitoring False Rejection berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $reject = Reject::where('uuid', $uuid)->firstOrFail();
        return view('form.reject.edit', compact('reject'));
    }

    public function update(Request $request, string $uuid)
    {
        $reject = Reject::where('uuid', $uuid)->firstOrFail();

        // ambil username_updated & nama_produksi
        $username_updated = Auth::user()->username;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTM';

        $cleanString = fn($str) => is_string($str) ? trim(preg_replace('/\s+/', ' ', $str)) : $str;

        $request->validate([
            'date'                => 'required|date',
            'shift'               => 'required',
            'nama_produk'         => 'required',
            'kode_produksi'       => 'required',
            'nama_mesin'          => 'required',
            'jumlah_tidak_lolos'  => 'nullable|integer',
            'jumlah_kontaminan'   => 'nullable|integer',
            'jenis_kontaminan'    => 'nullable|string',
            'posisi_kontaminan'   => 'nullable|string',
            'false_rejection'     => 'nullable|string',
            'catatan'             => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'shift', 'nama_produk', 'kode_produksi', 'nama_mesin',
            'jumlah_tidak_lolos', 'jumlah_kontaminan', 'jenis_kontaminan',
            'posisi_kontaminan', 'false_rejection', 'catatan'
        ]);

        $data['nama_produk']   = $cleanString($data['nama_produk']);
        $data['kode_produksi'] = $cleanString($data['kode_produksi']);

        $data['username_updated'] = $username_updated;
        $data['nama_produksi']    = $nama_produksi;

        $reject->update($data);

        // update tgl_update_produksi = updated_at +1 jam
        $reject->update(['tgl_update_produksi' => Carbon::parse($reject->updated_at)->addHour()]);

        return redirect()->route('reject.index')->with('success', 'Data Monitoring False Rejection berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Reject::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('username_updated', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%")
                ->orWhere('kode_produksi', 'like', "%{$search}%")
                ->orWhere('nama_mesin', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.reject.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $reject = Reject::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $reject->status_spv = $request->status_spv;
        $reject->catatan_spv = $request->catatan_spv;
        $reject->nama_spv = Auth::user()->username;
        $reject->tgl_update_spv = now();
        $reject->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('reject.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }


    public function destroy(string $uuid)
    {
        $reject = Reject::where('uuid', $uuid)->firstOrFail();
        $reject->delete();

        return redirect()->route('reject.index')->with('success', 'Data Monitoring False Rejection berhasil dihapus');
    }
}

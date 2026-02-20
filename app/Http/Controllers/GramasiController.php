<?php

namespace App\Http\Controllers;

use App\Models\Gramasi;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GramasiController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Gramasi::query()
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

        return view('form.gramasi.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.gramasi.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:1,2,3',
            'nama_produk' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'jenis_topping.*' => 'nullable|string|max:255',
            'standar.*' => 'nullable|numeric',
            'gramasi_1.*' => 'nullable|numeric',
            'gramasi_2.*' => 'nullable|numeric',
            'gramasi_3.*' => 'nullable|numeric',
            'pukul_1' => 'nullable|date_format:H:i',
            'pukul_2' => 'nullable|date_format:H:i',
            'pukul_3' => 'nullable|date_format:H:i',
            'tindakan_koreksi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only(['date','shift','nama_produk','kode_produksi','tindakan_koreksi','catatan']);
        $data['username'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;
        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        // Ambil input gramasi topping
        $jenis   = $request->input('jenis_topping', []);
        $standar = $request->input('standar', []);
        $gramasi1 = $request->input('gramasi_1', []);
        $gramasi2 = $request->input('gramasi_2', []);
        $gramasi3 = $request->input('gramasi_3', []);
        $pukul1 = $request->input('pukul_1');
        $pukul2 = $request->input('pukul_2');
        $pukul3 = $request->input('pukul_3');

        $gramasi_topping = [];
        foreach ($jenis as $i => $j) {
            if (!empty($j)) {
                $gramasi_topping[] = [
                    'jenis_topping' => $j,
                    'standar'       => $standar[$i] ?? null,
                    'pukul_1'       => $pukul1,
                    'gramasi_1'     => $gramasi1[$i] ?? null,
                    'pukul_2'       => $pukul2,
                    'gramasi_2'     => $gramasi2[$i] ?? null,
                    'pukul_3'       => $pukul3,
                    'gramasi_3'     => $gramasi3[$i] ?? null,
                ];
            }
        }
        $data['gramasi_topping'] = json_encode($gramasi_topping, JSON_UNESCAPED_UNICODE);

        $gramasi = Gramasi::create($data);

        // set tgl_update_produksi = created_at + 1 jam
        $gramasi->update(['tgl_update_produksi' => Carbon::parse($gramasi->created_at)->addHour()]);

        return redirect()->route('gramasi.index')
        ->with('success', 'Data Verifikasi Gramasi Topping berhasil disimpan');
    }

    public function edit($uuid)
    {
        $gramasi = Gramasi::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();
        $gramasi_toppingData = json_decode($gramasi->gramasi_topping ?? '[]', true);

        return view('form.gramasi.edit', compact('gramasi', 'produks', 'gramasi_toppingData'));
    }

    public function update(Request $request, $uuid)
    {
        $gramasi = Gramasi::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:1,2,3',
            'nama_produk' => 'required|string|max:255',
            'kode_produksi' => 'required|string|max:255',
            'jenis_topping.*' => 'nullable|string|max:255',
            'standar.*' => 'nullable|numeric',
            'gramasi_1.*' => 'nullable|numeric',
            'gramasi_2.*' => 'nullable|numeric',
            'gramasi_3.*' => 'nullable|numeric',
            'pukul_1' => 'nullable|date_format:H:i',
            'pukul_2' => 'nullable|date_format:H:i',
            'pukul_3' => 'nullable|date_format:H:i',
            'tindakan_koreksi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->only(['date','shift','nama_produk','kode_produksi','tindakan_koreksi','catatan']);

        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        // Ambil input gramasi topping
        $jenis   = $request->input('jenis_topping', []);
        $standar = $request->input('standar', []);
        $gramasi1 = $request->input('gramasi_1', []);
        $gramasi2 = $request->input('gramasi_2', []);
        $gramasi3 = $request->input('gramasi_3', []);
        $pukul1 = $request->input('pukul_1');
        $pukul2 = $request->input('pukul_2');
        $pukul3 = $request->input('pukul_3');

        $gramasi_topping = [];
        foreach ($jenis as $i => $j) {
            if (!empty($j)) {
                $gramasi_topping[] = [
                    'jenis_topping' => $j,
                    'standar'       => $standar[$i] ?? null,
                    'pukul_1'       => $pukul1,
                    'gramasi_1'     => $gramasi1[$i] ?? null,
                    'pukul_2'       => $pukul2,
                    'gramasi_2'     => $gramasi2[$i] ?? null,
                    'pukul_3'       => $pukul3,
                    'gramasi_3'     => $gramasi3[$i] ?? null,
                ];
            }
        }

        $data['gramasi_topping'] = json_encode($gramasi_topping, JSON_UNESCAPED_UNICODE);
        $data['tgl_update_produksi'] = Carbon::parse(now())->addHour();

        $gramasi->update($data);

        return redirect()->route('gramasi.index')
        ->with('success', 'Data Verifikasi Gramasi Topping berhasil diupdate.');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Gramasi::query()
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

        return view('form.gramasi.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $gramasi = Gramasi::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $gramasi->status_spv = $request->status_spv;
        $gramasi->catatan_spv = $request->catatan_spv;
        $gramasi->nama_spv = Auth::user()->username;
        $gramasi->tgl_update_spv = now();
        $gramasi->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('gramasi.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $gramasi = Gramasi::where('uuid', $uuid)->firstOrFail();
        $gramasi->delete();
        return redirect()->route('gramasi.verification')->with('success', 'Gramasi berhasil dihapus');
    }

    public function recyclebin()
    {
        $gramasi = Gramasi::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.gramasi.recyclebin', compact('gramasi'));
    }
    public function restore($uuid)
    {
        $gramasi = Gramasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $gramasi->restore();

        return redirect()->route('gramasi.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $gramasi = Gramasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $gramasi->forceDelete();

        return redirect()->route('gramasi.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
}

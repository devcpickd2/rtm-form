<?php

namespace App\Http\Controllers;

use App\Models\Iqf;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IqfController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Iqf::query()
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

        foreach ($data as $row) {
            if (is_string($row->suhu_pusat)) {
                $decoded = json_decode($row->suhu_pusat, true);
                $row->suhu_pusat = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($row->suhu_pusat)) {
                $row->suhu_pusat = [];
            }
        }

        return view('form.iqf.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.iqf.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required',
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'no_iqf' => 'nullable|string',
            'pukul' => 'nullable|date_format:H:i',
            'std_suhu' => 'nullable|numeric',
            'average' => 'nullable|numeric',
            'problem' => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
            'catatan' => 'nullable|string',
            'suhu_pusat' => 'nullable|array',
        ]);

        $suhuInput = $request->input('suhu_pusat', []);
        $suhu = [];
        for ($i = 1; $i <= 10; $i++) {
            $suhu[] = [
                'value' => $suhuInput[$i]['value'] ?? '',
                'ket'   => $suhuInput[$i]['ket'] ?? '',
            ];
        }

        $iqf = Iqf::create([
            'date' => $request->date,
            'shift' => $request->shift,
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'no_iqf' => $request->no_iqf,
            'pukul' => $request->pukul,
            'std_suhu' => $request->std_suhu !== null ? (float)$request->std_suhu : null,
            'average' => $request->average !== null ? (float)$request->average : null,
            'problem' => $request->problem,
            'tindakan_koreksi' => $request->tindakan_koreksi,
            'catatan' => $request->catatan,
            'suhu_pusat' => $suhu,
            'username' => Auth::user()->username,
            'nama_produksi' => session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : null,
            'status_produksi' => "1",
            'status_spv' => "0",
        ]);

        // Set tgl_update_produksi = created_at + 1 jam
        $iqf->update(['tgl_update_produksi' => Carbon::parse($iqf->created_at)->addHour()]);

        return redirect()->route('iqf.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit($uuid)
    {
        $iqf = Iqf::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();

    // Ambil data suhu_pusat
        $suhu_pusat = $iqf->suhu_pusat ?? [];

    // Jika masih string JSON, decode menjadi array
        if (is_string($suhu_pusat)) {
            $decoded = json_decode($suhu_pusat, true);
            $suhu_pusat = is_array($decoded) ? $decoded : [];
        }

    // Pastikan index 0-9 ada
        for ($i = 0; $i < 10; $i++) {
            if (!isset($suhu_pusat[$i])) {
                $suhu_pusat[$i] = [
                    'value' => '',
                    'ket'   => ''
                ];
            }
        }

        return view('form.iqf.edit', compact('iqf', 'produks', 'suhu_pusat'));
    }



    public function update(Request $request, $uuid)
    {
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required',
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'no_iqf' => 'nullable|string',
            'pukul' => 'nullable',
            'std_suhu' => 'nullable|numeric',
            'average' => 'nullable|numeric',
            'problem' => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
            'catatan' => 'nullable|string',
            'suhu_pusat' => 'nullable|array',
        ]);

        $iqf = Iqf::where('uuid', $uuid)->firstOrFail();

    // Ambil input suhu
        $input = $request->input('suhu_pusat', []);
        $suhu = [];

        for ($i = 0; $i < 10; $i++) {
            $suhu[$i] = [
                'value' => $input[$i]['value'] ?? '',
                'ket'   => $input[$i]['ket'] ?? '',
            ];
        }

        $iqf->update([
            'date' => $request->date,
            'shift' => $request->shift,
            'nama_produk' => $request->nama_produk,
            'kode_produksi' => $request->kode_produksi,
            'no_iqf' => $request->no_iqf,
            'pukul' => $request->pukul,
            'std_suhu' => $request->std_suhu,
            'average' => $request->average,
            'problem' => $request->problem,
            'tindakan_koreksi' => $request->tindakan_koreksi,
            'catatan' => $request->catatan,

        // CAST akan otomatis encode JSON
            'suhu_pusat' => $suhu,

            'username_updated' => Auth::user()->username,
            'nama_produksi' => session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : null,
            'tgl_update_produksi' => now()->addHour(),
        ]);

        return redirect()->route('iqf.index')->with('success', 'Data berhasil diupdate');
    }

    public function verification(Request $request)
    {

        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Iqf::query()
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

        foreach ($data as $row) {
            if (is_string($row->suhu_pusat)) {
                $decoded = json_decode($row->suhu_pusat, true);
                $row->suhu_pusat = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($row->suhu_pusat)) {
                $row->suhu_pusat = [];
            }
        }

        return view('form.iqf.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $iqf = Iqf::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $iqf->status_spv = $request->status_spv;
        $iqf->catatan_spv = $request->catatan_spv;
        $iqf->nama_spv = Auth::user()->username;
        $iqf->tgl_update_spv = now();
        $iqf->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('iqf.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $iqf = Iqf::where('uuid', $uuid)->firstOrFail();
        $iqf->delete();
        return redirect()->route('iqf.verification')->with('success', 'IQF berhasil dihapus');
    }

    public function recyclebin()
    {
        $iqf = Iqf::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.iqf.recyclebin', compact('iqf'));
    }
    public function restore($uuid)
    {
        $iqf = Iqf::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $iqf->restore();

        return redirect()->route('iqf.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $iqf = Iqf::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $iqf->forceDelete();

        return redirect()->route('iqf.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
}

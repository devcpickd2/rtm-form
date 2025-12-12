<?php

namespace App\Http\Controllers;

use App\Models\Kontaminasi;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class KontaminasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');

        $data = Kontaminasi::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produksi', 'like', "%{$search}%")
            ->orWhere('jenis_kontaminasi', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('pukul', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.kontaminasi.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.kontaminasi.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'             => 'required|date',
            'shift'            => 'required',
            'pukul'            => 'required',
            'jenis_kontaminasi'=> 'required',
            'bukti'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_produk'      => 'required',
            'kode_produksi'    => 'required',
            'tahapan'          => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
            'catatan'          => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'pukul', 'shift',
            'jenis_kontaminasi', 'nama_produk', 'kode_produksi',
            'tahapan', 'tindakan_koreksi', 'catatan'
        ]);

        // Init Intervention Image
        $manager = new ImageManager(new Driver());

        // ==== Upload + Compress ====
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = 'kontaminasi_' . time() . '.jpg';

            $image = $manager->read($file)->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            Storage::disk('public')->put(
                "uploads/kontaminasi/$filename",
                $image->toJpeg(75)
            );

            $data['bukti'] = "uploads/kontaminasi/$filename";
        }

        // Username & Produksi
        $data['username'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? User::where('uuid', session('selected_produksi'))->value('name')
        : null;

        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        $kontaminasi = Kontaminasi::create($data);

        $kontaminasi->update([
            'tgl_update_produksi' => Carbon::parse($kontaminasi->created_at)->addHour()
        ]);

        return redirect()->route('kontaminasi.index')
        ->with('success', 'Data Kontaminasi berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $produks = Produk::all();
        $kontaminasi = Kontaminasi::where('uuid', $uuid)->firstOrFail();
        return view('form.kontaminasi.edit', compact('kontaminasi', 'produks'));
    }

    public function update(Request $request, string $uuid)
    {
        $kontaminasi = Kontaminasi::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'             => 'required|date',
            'shift'            => 'required',
            'pukul'            => 'required',
            'jenis_kontaminasi'=> 'required',
            'bukti'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_produk'      => 'required',
            'kode_produksi'    => 'required',
            'tahapan'          => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
            'catatan'          => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'pukul', 'shift',
            'jenis_kontaminasi', 'nama_produk', 'kode_produksi',
            'tahapan', 'tindakan_koreksi', 'catatan'
        ]);

        $manager = new ImageManager(new Driver());

        // ==== UPDATE + COMPRESS FILE BARU ====
        if ($request->hasFile('bukti')) {

            // Hapus file lama
            if ($kontaminasi->bukti && Storage::disk('public')->exists($kontaminasi->bukti)) {
                Storage::disk('public')->delete($kontaminasi->bukti);
            }

            $file = $request->file('bukti');
            $filename = 'kontaminasi_' . time() . '.jpg';

            $image = $manager->read($file)->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            Storage::disk('public')->put(
                "uploads/kontaminasi/$filename",
                $image->toJpeg(75)
            );

            $data['bukti'] = "uploads/kontaminasi/$filename";
        }

        // Update user info
        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? User::where('uuid', session('selected_produksi'))->value('name')
        : null;

        $kontaminasi->update($data);

        $kontaminasi->update([
            'tgl_update_produksi' => Carbon::parse($kontaminasi->updated_at)->addHour()
        ]);

        return redirect()->route('kontaminasi.index')
        ->with('success', 'Data Kontaminasi berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Kontaminasi::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produksi', 'like', "%{$search}%")
            ->orWhere('jenis_kontaminasi', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('pukul', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.kontaminasi.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $kontaminasi = Kontaminasi::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $kontaminasi->status_spv = $request->status_spv;
        $kontaminasi->catatan_spv = $request->catatan_spv;
        $kontaminasi->nama_spv = Auth::user()->username;
        $kontaminasi->tgl_update_spv = now();
        $kontaminasi->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('kontaminasi.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $kontaminasi = Kontaminasi::where('uuid', $uuid)->firstOrFail();
        $kontaminasi->delete();
        return redirect()->route('kontaminasi.verification')->with('success', 'Kontaminasi berhasil dihapus');
    }

    public function recyclebin()
    {
        $kontaminasi = Kontaminasi::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.kontaminasi.recyclebin', compact('kontaminasi'));
    }
    public function restore($uuid)
    {
        $kontaminasi = Kontaminasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $kontaminasi->restore();

        return redirect()->route('kontaminasi.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $kontaminasi = Kontaminasi::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $kontaminasi->forceDelete();

        return redirect()->route('kontaminasi.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pengemasan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PengemasanController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Pengemasan::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
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

        return view('form.pengemasan.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.pengemasan.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'pukul'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',

    // maksimal 2MB
            'tray_checking.kode_produksi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'box_checking.kode_produksi'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tray_packing.kode_produksi'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'box_packing.kode_produksi'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]); 

    // Intervention v3 proper init
        $manager = new ImageManager(new Driver());

    // Helper upload
        $uploadFile = function($file) use ($manager) {
            if (!$file) return null;

            $filename = time() . '-' . uniqid() . '.jpg';
            
            $image = $manager->read($file);

            $image->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            $compressed = $image->toJpeg(70);

            Storage::disk('public')->put("uploads/pengemasan/$filename", $compressed);

            return "uploads/pengemasan/$filename";
        };

    // Ambil array input dulu
        $trayChecking  = $request->input('tray_checking', []);
        $boxChecking   = $request->input('box_checking', []);
        $trayPacking   = $request->input('tray_packing', []);
        $boxPacking    = $request->input('box_packing', []);

    // Upload jika ada file
       // Upload jika ada file
        $trayChecking['kode_produksi'] = $uploadFile($request->file('tray_checking.kode_produksi'));
        $boxChecking['kode_produksi']  = $uploadFile($request->file('box_checking.kode_produksi'));
        $trayPacking['kode_produksi']  = $uploadFile($request->file('tray_packing.kode_produksi'));
        $boxPacking['kode_produksi']   = $uploadFile($request->file('box_packing.kode_produksi'));

    // Simpan database
        $data = $request->only([
            'date','shift','pukul','nama_produk','kode_produksi',
            'keterangan_checking','keterangan_packing','catatan'
        ]);

        $data['username'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : null;
        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";
        $data['tray_checking'] = json_encode($trayChecking);
        $data['box_checking']  = json_encode($boxChecking);
        $data['tray_packing']  = json_encode($trayPacking);
        $data['box_packing']   = json_encode($boxPacking);

        $save = Pengemasan::create($data);
        $save->update(['tgl_update_produksi' => Carbon::parse($save->created_at)->addHour()]);

        return redirect()->route('pengemasan.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $pengemasan = Pengemasan::where('uuid', $uuid)->firstOrFail();
        $produks = Produk::all();

    // UBAH JSON → ARRAY (langsung masuk ke model agar blade mudah aksesnya)
        $pengemasan->tray_checking = json_decode($pengemasan->tray_checking, true) ?? [];
        $pengemasan->box_checking  = json_decode($pengemasan->box_checking, true) ?? [];
        $pengemasan->tray_packing  = json_decode($pengemasan->tray_packing, true) ?? [];
        $pengemasan->box_packing   = json_decode($pengemasan->box_packing, true) ?? [];

        return view('form.pengemasan.edit', compact(
            'pengemasan','produks'
        ));
    }

    public function update(Request $request, string $uuid)
    {
        $pengemasan = Pengemasan::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'pukul'       => 'required',
            'nama_produk' => 'required',
            'kode_produksi' => 'required',

            'tray_checking.kode_produksi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'box_checking.kode_produksi'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tray_packing.kode_produksi'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'box_packing.kode_produksi'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $manager = new ImageManager(new Driver());

    // FUNCTION UPLOAD
        $upload = function ($file, $old = null) use ($manager) {
            if (!$file) return $old;

        // delete old file
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $filename = time() . '-' . uniqid() . '.jpg';

            $img = $manager->read($file);
            $img->resize(1280, 1280, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            $compressed = $img->toJpeg(70);

            Storage::disk('public')->put("uploads/pengemasan/$filename", $compressed);

            return "uploads/pengemasan/$filename";
        };


    // LOAD JSON VALUE LAMA
        $oldTray   = json_decode($pengemasan->tray_checking, true) ?? [];
        $oldBox    = json_decode($pengemasan->box_checking, true) ?? [];
        $oldTPack  = json_decode($pengemasan->tray_packing, true) ?? [];
        $oldBPack  = json_decode($pengemasan->box_packing, true) ?? [];


    // AMBIL INPUT (TETAP ARRAY)
        $trayChecking  = $request->input('tray_checking', []);
        $boxChecking   = $request->input('box_checking', []);
        $trayPacking   = $request->input('tray_packing', []);
        $boxPacking    = $request->input('box_packing', []);


    // UPLOAD FILE (DOT SYNTAX)
        $trayChecking['kode_produksi'] = $upload(
            $request->file('tray_checking.kode_produksi'),
            $oldTray['kode_produksi'] ?? null
        );

        $boxChecking['kode_produksi'] = $upload(
            $request->file('box_checking.kode_produksi'),
            $oldBox['kode_produksi'] ?? null
        );

        $trayPacking['kode_produksi'] = $upload(
            $request->file('tray_packing.kode_produksi'),
            $oldTPack['kode_produksi'] ?? null
        );

        $boxPacking['kode_produksi'] = $upload(
            $request->file('box_packing.kode_produksi'),
            $oldBPack['kode_produksi'] ?? null
        );


    // UPDATE FIELD UTAMA
        $data = $request->only([
            'date','shift','pukul','nama_produk','kode_produksi',
            'keterangan_checking','keterangan_packing','catatan'
        ]);

        $data['username_updated'] = Auth::user()->username;

    // SIMPAN JSON
        $data['tray_checking'] = json_encode($trayChecking);
        $data['box_checking']  = json_encode($boxChecking);
        $data['tray_packing']  = json_encode($trayPacking);
        $data['box_packing']   = json_encode($boxPacking);

        $pengemasan->update($data);


    // UPDATE WAKTU
        $pengemasan->update([
            'tgl_update_produksi' => Carbon::parse($pengemasan->updated_at)->addHour()
        ]);

        return redirect()->route('pengemasan.index')
        ->with('success', 'Data berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Pengemasan::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
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

        return view('form.pengemasan.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $pengemasan = Pengemasan::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $pengemasan->status_spv = $request->status_spv;
        $pengemasan->catatan_spv = $request->catatan_spv;
        $pengemasan->nama_spv = Auth::user()->username;
        $pengemasan->tgl_update_spv = now();
        $pengemasan->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('pengemasan.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    // public function destroy(string $uuid)
    // {
    //     $pengemasan = Pengemasan::where('uuid', $uuid)->firstOrFail();

    //     // Hapus semua file jika ada
    //     $trayChecking = json_decode($pengemasan->tray_checking, true) ?? [];
    //     $boxChecking  = json_decode($pengemasan->box_checking, true) ?? [];
    //     $trayPacking  = json_decode($pengemasan->tray_packing, true) ?? [];
    //     $boxPacking   = json_decode($pengemasan->box_packing, true) ?? [];

    //     foreach ([$trayChecking, $boxChecking, $trayPacking, $boxPacking] as $fileArray) {
    //         if (!empty($fileArray['kode_produksi']) && Storage::disk('public')->exists($fileArray['kode_produksi'])) {
    //             Storage::disk('public')->delete($fileArray['kode_produksi']);
    //         }
    //     }

    //     $pengemasan->delete();

    //     return redirect()->route('pengemasan.index')
    //     ->with('success', 'Data Pemeriksaan Pengemasan berhasil dihapus');
    // }

    public function destroy($uuid)
    {
        $pengemasan = Pengemasan::where('uuid', $uuid)->firstOrFail();
        $pengemasan->delete();
        return redirect()->route('pengemasan.verification')->with('success', 'Pengemasan berhasil dihapus');
    }

    public function recyclebin()
    {
        $pengemasan = Pengemasan::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.pengemasan.recyclebin', compact('pengemasan'));
    }
    public function restore($uuid)
    {
        $pengemasan = Pengemasan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pengemasan->restore();

        return redirect()->route('pengemasan.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $pengemasan = Pengemasan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pengemasan->forceDelete();

        return redirect()->route('pengemasan.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

}

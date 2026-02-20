<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Plant; 
// Import model Plant agar bisa digunakan di controller

class PlantController extends Controller
{
    // Menampilkan semua data plant
    public function index()
    {
        // Ambil semua data dari tabel plant
        $plant = Plant::all();

        // Kirim data ke view plant.index
        return view('plant.index', compact('plant'));
    }

    // Tampilkan form create
    public function create()
    {
        // Load view form tambah plant
        return view('plant.create'); 
    }

    // Simpan data baru ke database
    public function store(Request $request)
    {
        $username = Auth::user()->username;
        // Validasi input agar plant wajib diisi dan maksimal 255 karakter
        $request->validate([
            'plant' => 'required|string|max:255'
        ]);

        // Ambil username dari session, jika tidak ada gunakan default 'putri'
        // $username = session('username', 'putri');

        // Simpan data ke tabel plant
        Plant::create([
            'username' => $username,
            'plant' => $request->plant
        ]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('plant.index')->with('success', 'Plant berhasil ditambahkan');
    }

    // Tampilkan form edit berdasarkan UUID
    public function edit($uuid)
    {
        // Cari data plant berdasarkan UUID. Jika tidak ada, tampilkan error 404
        $plant = Plant::where('uuid', $uuid)->firstOrFail();

        // Kirim data ke view edit
        return view('plant.edit', compact('plant'));
    }

    // Update data plant berdasarkan UUID
    public function update(Request $request, $uuid)
    {
        // Validasi input
        $request->validate([
            'plant' => 'required|string|max:255'
        ]);

        // Cari plant berdasarkan UUID. Jika tidak ketemu, tampilkan 404
        $plant = Plant::where('uuid', $uuid)->firstOrFail();

        // Update kolom plant saja
        $plant->update([
            'plant' => $request->plant
        ]);

        // Redirect ke halaman index
        return redirect()->route('plant.index')->with('success', 'Plant berhasil diupdate');
    }

    // Hapus data plant berdasarkan UUID
    public function destroy($uuid)
    {
        // Cari data plant berdasarkan UUID
        $plant = Plant::where('uuid', $uuid)->firstOrFail();

        // Hapus data
        $plant->delete();

        // Redirect ke halaman index
        return redirect()->route('plant.index')->with('success', 'Plant berhasil dihapus');
    }
}



// //model binding beda penulisan bagian yang menggunakan uuid sebagai parameternya

//     // Menampilkan form edit untuk data tertentu (binding otomatis)
// public function edit(Plant $plant)
// {
//         // $plant otomatis terisi berdasarkan UUID karena Route Model Binding
//     return view('plant.edit', compact('plant'));
// }

//     // Mengupdate data yang sudah ada (binding otomatis)
// public function update(Request $request, Plant $plant)
// {
//         // Validasi input plant wajib diisi dan maksimal 255 karakter
//     $request->validate([
//         'plant' => 'required|string|max:255'
//     ]);

//         // Update kolom plant di database
//     $plant->update([
//         'plant' => $request->plant
//     ]);

//         // Redirect ke halaman index dengan pesan sukses
//     return redirect()->route('plant.index')->with('success', 'Plant berhasil diupdate');
// }

//     // Menghapus data (binding otomatis)
// public function destroy(Plant $plant)
// {
//         // Hapus data dari database
//     $plant->delete();

//         // Redirect kembali ke halaman index dengan pesan sukses
//     return redirect()->route('plant.index')->with('success', 'Plant berhasil dihapus');
// }

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suhu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Ambil tanggal, default hari ini
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // ✅ Cache data suhu agar query tidak berat
        $data = Cache::remember("suhu_{$tanggal}", 60, function () use ($tanggal) {
            return Suhu::whereDate('date', $tanggal)
                        ->orderBy('pukul', 'asc')
                        ->limit(500)
                        ->get();
        });

        $user = Auth::user();

        // ✅ Tampilkan popup hanya untuk user type_user = 4
        if ($user && $user->type_user == 4 && 
            !session()->has('selected_produksi') && 
            !session()->has('modal_shown')) {

            $produksi = User::where('type_user', 3)->get();

            session([
                'pop_up_produksi' => $produksi,
                'modal_shown' => true
            ]);
        }

        // ✅ Ambil produksi yang sedang dipilih
        $selectedProduksi = null;
        if (session()->has('selected_produksi')) {
            $selectedProduksi = User::where('uuid', session('selected_produksi'))->first();
        }

        return view('dashboard', compact('data', 'tanggal', 'selectedProduksi'));
    }

    public function setProduksi(Request $request)
    {
        $request->validate([
            'nama_produksi' => 'required|exists:users,uuid',
        ]);

        session([
            'selected_produksi' => $request->nama_produksi,
            'modal_shown' => true
        ]);

        session()->forget('pop_up_produksi');

        return redirect()->route('dashboard');
    }

    // ✅ Untuk AJAX refresh suhu data (opsional)
    public function getData(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        $data = Cache::remember("suhu_ajax_{$tanggal}", 60, function () use ($tanggal) {
            return Suhu::whereDate('date', $tanggal)
                        ->orderBy('pukul', 'asc')
                        ->limit(500)
                        ->get();
        });

        return response()->json($data);
    }
}

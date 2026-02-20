<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suhu;
use App\Models\User;
use App\Models\Cooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

 public function index(Request $request)
 {
    $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

    $data = Cache::remember("suhu_{$tanggal}", 60, function () use ($tanggal) {
        return Suhu::whereDate('date', $tanggal)
        ->orderBy('pukul', 'asc')
        ->limit(500)
        ->get();
    });

    $user = Auth::user();

    if (
        $user &&
        $user->type_user == 4 &&
        !session()->has('selected_produksi') &&
        !session()->has('modal_shown')
    ) {
        $produksi = User::where('type_user', 3)->get();

        session([
            'pop_up_produksi' => $produksi,
            'modal_shown' => true
        ]);
    }

    $selectedProduksi = null;
    if (session()->has('selected_produksi')) {
        $selectedProduksi = User::where('uuid', session('selected_produksi'))->first();
    }

    $today = Carbon::today();

    // ================== COOKING ==================

    $totalCookingHariIni = Cooking::whereDate('date', $today)
    ->select('nama_produk', 'kode_produksi')
    ->distinct()
    ->get()
    ->count();



    $listCookingHariIni = Cooking::whereDate('date', $today)
    ->orderBy('created_at', 'desc')
    ->get(['nama_produk', 'kode_produksi']);

    $jamCooking = Cooking::whereDate('date', $today)
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get([
        'nama_produk',
        'sub_produk',  
        'kode_produksi',
        'waktu_mulai',
        'waktu_selesai'
    ]);

    $lastCooking = Cooking::whereDate('date', $today)
    ->orderBy('created_at', 'desc')
    ->first();

    // ================== PROGRESS (FINAL) ==================

// ================== PROGRESS (FIX FINAL) ==================

    $progressCooking = 0;
    $statusCooking   = 'Belum Mulai';

    if ($lastCooking && $lastCooking->waktu_mulai) {

        $now   = Carbon::now();
        $start = Carbon::parse($lastCooking->waktu_mulai);

        if ($lastCooking->waktu_selesai) {

            $end = Carbon::parse($lastCooking->waktu_selesai);

            if ($now->lt($start)) {
            // 🔴 Belum mulai
                $statusCooking   = 'Belum Mulai';
                $progressCooking = 0;

            } elseif ($now->between($start, $end)) {
            // 🔵 Sedang proses
                $total   = $start->diffInSeconds($end);
                $current = $start->diffInSeconds($now);

                $progressCooking = $total > 0
                ? round(($current / $total) * 100)
                : 0;

                $statusCooking = 'Sedang Proses';

            } else {
            // 🟢 Selesai
                $statusCooking   = 'Selesai';
                $progressCooking = 100;
            }

        } else {
        // 🔵 Jam selesai belum diisi
            if ($now->lt($start)) {
                $statusCooking   = 'Belum Mulai';
                $progressCooking = 0;
            } else {
                $statusCooking   = 'Sedang Proses';
                $progressCooking = min(
                    round($start->diffInMinutes($now) * 2),
                    99
                );
            }
        }
    }


    return view('dashboard', compact(
        // suhu
        'data',
        'tanggal',
        'selectedProduksi',

        // cooking
        'totalCookingHariIni',
        'listCookingHariIni',
        'jamCooking',
        'lastCooking',
        'progressCooking',
        'statusCooking'
    ));
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

    // ===============================
    // ✅ AJAX REFRESH SUHU (EXISTING)
    // ===============================
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

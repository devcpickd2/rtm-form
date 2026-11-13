<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suhu;
use App\Models\User;
use App\Models\Cooking;
use App\Models\Steamer;
use App\Models\Kontaminasi;
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
        // Ambil nama produksi terbaru dari tabel suhus
    $namaProduks = Suhu::whereDate('date', $today)
    ->orderBy('created_at', 'desc')
    ->value('nama_produksi');

    // ================== COOKING ==================

    $totalCookingHariIni = Cooking::whereDate('date', $today)
    ->select('nama_produk', 'kode_produksi')
    ->distinct()
    ->get()
    ->count();

    $listCookingHariIni = Cooking::whereDate('date', $today)
    ->orderBy('created_at', 'desc')
    ->get(['nama_produk', 'kode_produksi']);

    // Tidak lagi dibatasi limit(5) — seluruh data cooking hari ini
    // dikirim ke view dan ditampilkan lewat list yang auto-scroll.
    $jamCooking = Cooking::whereDate('date', $today)
    ->orderBy('created_at', 'desc')
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

    $latestSuhu = $data->isNotEmpty() ? $data->last() : null;

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

// ================== STEAMER ==================
// Kolom detail (t_ruang, t_produk, waktu, jam_mulai, jam_selesai, kode_produksi dll)
// tersimpan sebagai array di dalam kolom json 'steaming'.

    $steamerRecordsHariIni = Steamer::whereDate('date', $today)
    ->orderBy('created_at', 'desc')
    ->get();

    $steamerHariIni = collect();

    foreach ($steamerRecordsHariIni as $record) {

        $steamingItems = json_decode($record->steaming, true) ?? [];

        foreach ($steamingItems as $item) {

            $steamerHariIni->push(
                (object) array_merge($item, [
                    'nama_produk' => $record->nama_produk,
                    'created_at'  => $record->created_at,
                ])
            );

        }
    }

// Tetap ambil Steamer terakhir seperti sebelumnya
    $lastSteamerRecord = $steamerRecordsHariIni->first();

    $lastSteamer = null;

    if ($lastSteamerRecord) {

        $steamingItems = json_decode($lastSteamerRecord->steaming, true) ?? [];

        if (!empty($steamingItems)) {

        // Ambil entri terakhir dari array steaming
            $lastItem = end($steamingItems);

            $lastSteamer = (object) array_merge($lastItem, [
                'nama_produk' => $lastSteamerRecord->nama_produk,
            ]);
        }
    }

    // ================== KONTAMINASI ==================
    $bulanIni = Carbon::now();

    $totalKontaminasiBulanIni = Kontaminasi::whereMonth('date', $bulanIni->month)
    ->whereYear('date', $bulanIni->year)
    ->count();

    $kontaminasiPerJenis = Kontaminasi::whereMonth('date', $bulanIni->month)
    ->whereYear('date', $bulanIni->year)
    ->select('jenis_kontaminasi', DB::raw('count(*) as jumlah'))
    ->groupBy('jenis_kontaminasi')
    ->get()
    ->map(function ($item) use ($totalKontaminasiBulanIni) {
        return [
            'jenis_kontaminasi' => $item->jenis_kontaminasi,
            'persentase' => $totalKontaminasiBulanIni > 0
            ? round(($item->jumlah / $totalKontaminasiBulanIni) * 100)
            : 0,
        ];
    })
    ->toArray();

    $view = request()->routeIs('dashboard.led') ? 'dashboardled' : 'dashboard';

    return view($view, compact(
        'data',
        'latestSuhu',
        'tanggal',
        'selectedProduksi',
        'namaProduks',
        'totalCookingHariIni',
        'listCookingHariIni',
        'jamCooking',
        'lastCooking',
        'progressCooking',
        'statusCooking',
        'lastSteamer',
        'steamerHariIni',
        'totalKontaminasiBulanIni',
        'kontaminasiPerJenis'
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

<?php

namespace App\Http\Controllers;

use App\Models\Gmp;
use App\Models\Produksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
// excel
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class GmpController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Gmp::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('noodle_rice', 'like', "%{$search}%")
            ->orWhere('cooking', 'like', "%{$search}%")
            ->orWhere('packing', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.gmp.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $karyawanNoodle  = Produksi::where('area', 'Noodle & Rice')->pluck('nama_karyawan')->toArray();
        $karyawanCooking = Produksi::where('area', 'Cooking')->pluck('nama_karyawan')->toArray();
        $karyawanPacking = Produksi::where('area', 'Packing')->pluck('nama_karyawan')->toArray();

        return view('form.gmp.create', compact('karyawanNoodle', 'karyawanCooking', 'karyawanPacking'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $data = $request->only(['date']);
        $data['username'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? User::where('uuid', session('selected_produksi'))->first()->name
        : null;
        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        $areas = ['noodle_rice', 'cooking', 'packing'];
        foreach ($areas as $area) {
            $data[$area] = $request->input($area, []);
        }

        $gmp = Gmp::create($data);

        // Set tgl_update_produksi = created_at + 1 jam
        $gmp->update(['tgl_update_produksi' => Carbon::parse($gmp->created_at)->addHour()]);

        return redirect()->route('gmp.index')
        ->with('success', 'Data GMP Karyawan berhasil disimpan');
    }

    public function edit(string $uuid)
    {
        $gmp = Gmp::where('uuid', $uuid)->firstOrFail();

        $karyawanNoodle  = Produksi::where('area', 'Noodle & Rice')->pluck('nama_karyawan')->toArray();
        $karyawanCooking = Produksi::where('area', 'Cooking')->pluck('nama_karyawan')->toArray();
        $karyawanPacking = Produksi::where('area', 'Packing')->pluck('nama_karyawan')->toArray();

        return view('form.gmp.edit', compact('gmp', 'karyawanNoodle', 'karyawanCooking', 'karyawanPacking'));
    }

    public function update(Request $request, string $uuid)
    {
        $gmp = Gmp::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date' => 'required|date',
        ]);

        $data = $request->only(['date']);
        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi')
        ? User::where('uuid', session('selected_produksi'))->first()->name
        : null;

        $areas = ['noodle_rice', 'cooking', 'packing'];
        foreach ($areas as $area) {
            $data[$area] = $request->input($area, []);
        }

        $gmp->update($data);

        // Update tgl_update_produksi = updated_at + 1 jam
        $gmp->update(['tgl_update_produksi' => Carbon::parse($gmp->updated_at)->addHour()]);

        return redirect()->route('gmp.index')
        ->with('success', 'Data GMP Karyawan berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Gmp::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('noodle_rice', 'like', "%{$search}%")
            ->orWhere('cooking', 'like', "%{$search}%")
            ->orWhere('packing', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.gmp.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $gmp = Gmp::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $gmp->status_spv = $request->status_spv;
        $gmp->catatan_spv = $request->catatan_spv;
        $gmp->nama_spv = Auth::user()->username;
        $gmp->tgl_update_spv = now();
        $gmp->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('gmp.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy(string $uuid)
    {
        $gmp = Gmp::where('uuid', $uuid)->firstOrFail();
        $gmp->delete();

        return redirect()->route('gmp.index')
        ->with('success', 'Data GMP Karyawan berhasil dihapus');
    }

    public function export(Request $request)
    {
        $date = $request->input('date');       
        $atribut = $request->input('atribut'); 

        if (!$date || !$atribut) {
            return redirect()->route('gmp.verification')
            ->with('error', 'Pilih bulan dan atribut terlebih dahulu.');
        }

        try {
            [$tahun, $bulan] = explode('-', $date);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$bulan, (int)$tahun);

            $data = Gmp::whereNotNull($atribut)
            ->where('date', 'like', "$date%")
            ->orderBy('date', 'asc')
            ->get(['date', $atribut]);

            if ($data->isEmpty()) {
                return redirect()->route('gmp.verification')
                ->with('error', "Tidak ada data untuk bulan {$date}");
            }

            $rekap = [];
            foreach ($data as $row) {
                $tgl = (int)date('d', strtotime($row->date));
                $json = $row->$atribut; 
                if (!$json || !is_array($json)) continue;

                foreach ($json as $karyawan) {
                    $nama = $karyawan['nama_karyawan'];
                    if (!isset($rekap[$nama])) {
                        $rekap[$nama] = [];
                        for ($d = 1; $d <= $daysInMonth; $d++) {
                            $rekap[$nama][$d] = [
                                'seragam' => '',
                                'boot'    => '',
                                'masker'  => '',
                                'ciput'   => '',
                                'parfum'  => '',
                            ];
                        }
                    }

                    $rekap[$nama][$tgl] = [
                        'seragam' => $karyawan['seragam'],
                        'boot'    => $karyawan['boot'],
                        'masker'  => $karyawan['masker'],
                        'ciput'   => $karyawan['ciput'],
                        'parfum'  => $karyawan['parfum'],
                    ];
                }
            }

            $templatePath = storage_path('app/templates/gmp_karyawan.xlsx');
            $spreadsheet  = IOFactory::load($templatePath);
            $sheet        = $spreadsheet->getActiveSheet();

            $bulanTahun = \Carbon\Carbon::createFromFormat('Y-m', $date)->format('F Y');

            $atributMap = [
                'cooking'    => 'Cooking',
                'packing'    => 'Packing',
                'noodle_rice'=> 'Noodle & Rice',
            ];

            $atributDisplay = $atributMap[$atribut] ?? $atribut;

            $sheet->setCellValue('B3', $bulanTahun);
            $sheet->setCellValue('B4', $atributDisplay);

            $uniqueDates = $data->pluck('date')->unique()->sort()->values();

            $col = 2;
            $headerRow = 7;

            foreach ($uniqueDates as $tglFull) {
                $tglDay = date('d-m-Y', strtotime($tglFull));
                $sheet->setCellValueByColumnAndRow($col, $headerRow, $tglDay);
                $col += 5;
            }

            $totalColIndex = 157; 
            $sheet->setCellValueByColumnAndRow($totalColIndex, $headerRow, '');

            $rowNum = 15;
            foreach ($rekap as $nama => $harian) {
                $sheet->setCellValue("A{$rowNum}", $nama);

                $col = 2;
                $total = 0;

                foreach ($uniqueDates as $tglFull) {
                    $tglNum = (int)date('d', strtotime($tglFull)); 

                    $seragam = $harian[$tglNum]['seragam'] ?? 0;
                    $boot    = $harian[$tglNum]['boot']    ?? 0;
                    $masker  = $harian[$tglNum]['masker']  ?? 0;
                    $ciput   = $harian[$tglNum]['ciput']   ?? 0;
                    $parfum  = $harian[$tglNum]['parfum']  ?? 0;

                    $sheet->setCellValueByColumnAndRow($col,     $rowNum, $seragam);
                    $sheet->setCellValueByColumnAndRow($col + 1, $rowNum, $boot);
                    $sheet->setCellValueByColumnAndRow($col + 2, $rowNum, $masker);
                    $sheet->setCellValueByColumnAndRow($col + 3, $rowNum, $ciput);
                    $sheet->setCellValueByColumnAndRow($col + 4, $rowNum, $parfum);

                    $total += $seragam + $boot + $masker + $ciput + $parfum;

                    $col += 5;
                }

                $sheet->setCellValueByColumnAndRow($totalColIndex, $rowNum, $total);

                $lastColLetter = Coordinate::stringFromColumnIndex($totalColIndex);
                $sheet->getStyle("A{$rowNum}:{$lastColLetter}{$rowNum}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

                $rowNum++;
            }

            $filename = "Rekap_GMP_{$atribut}_{$date}.xlsx";
            if (ob_get_contents()) ob_end_clean();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (\Throwable $e) {
            \Log::error("Export GMP gagal: ".$e->getMessage());
            return redirect()
            ->route('gmp.verification')
            ->with('error', 'Gagal export: '.$e->getMessage());
        }
    }
}
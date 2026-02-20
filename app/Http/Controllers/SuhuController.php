<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suhu;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
// excel
use App\Models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SuhuController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Suhu::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produksi', 'like', "%{$search}%")
            ->orWhere('shift', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('pukul', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.suhu.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        return view('form.suhu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'  => 'required|date',
            'pukul' => 'required',
            'shift' => 'required',
            'chillroom'   => 'nullable|numeric',
            'cs_1'        => 'nullable|numeric',
            'cs_2'        => 'nullable|numeric',
            'anteroom_rm' => 'nullable|numeric',
            'seasoning_suhu' => 'nullable|numeric',
            'seasoning_rh'   => 'nullable|numeric',
            'rice'      => 'nullable|numeric',
            'noodle'    => 'nullable|numeric',
            'prep_room' => 'nullable|numeric',
            'cooking'   => 'nullable|numeric',
            'filling' => 'nullable|numeric',
            'topping' => 'nullable|numeric',
            'packing' => 'nullable|numeric',
            'ds_suhu' => 'nullable|numeric',
            'ds_rh'   => 'nullable|numeric',
            'cs_fg'       => 'nullable|numeric',
            'anteroom_fg' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
            'catatan'    => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'pukul', 'shift',
            'chillroom', 'cs_1', 'cs_2', 'anteroom_rm',
            'seasoning_suhu', 'seasoning_rh',
            'rice', 'noodle', 'prep_room', 'cooking',
            'filling', 'topping', 'packing',
            'ds_suhu', 'ds_rh',
            'cs_fg', 'anteroom_fg',
            'keterangan', 'catatan'
        ]);

        $data['username']      = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi') 
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name 
        : null;
        $data['status_produksi'] = "1";
        $data['status_spv'] = "0";

        $suhu = Suhu::create($data);

        // Set tgl_update_produksi = created_at + 1 jam
        $suhu->update(['tgl_update_produksi' => Carbon::parse($suhu->created_at)->addHour()]);

        return redirect()->route('suhu.index')->with('success', 'Data Suhu berhasil disimpan');
    }

    public function edit($uuid)
    {
        $suhu = Suhu::findOrFail($uuid);
        return view('form.suhu.edit', compact('suhu'));
    }

    public function update(Request $request, $uuid)
    {
        $suhu = Suhu::findOrFail($uuid);

        $request->validate([
            'date'  => 'required|date',
            'pukul' => 'required',
            'shift' => 'required',
            'chillroom'   => 'nullable|numeric',
            'cs_1'        => 'nullable|numeric',
            'cs_2'        => 'nullable|numeric',
            'anteroom_rm' => 'nullable|numeric',
            'seasoning_suhu' => 'nullable|numeric',
            'seasoning_rh'   => 'nullable|numeric',
            'rice'      => 'nullable|numeric',
            'noodle'    => 'nullable|numeric',
            'prep_room' => 'nullable|numeric',
            'cooking'   => 'nullable|numeric',
            'filling' => 'nullable|numeric',
            'topping' => 'nullable|numeric',
            'packing' => 'nullable|numeric',
            'ds_suhu' => 'nullable|numeric',
            'ds_rh'   => 'nullable|numeric',
            'cs_fg'       => 'nullable|numeric',
            'anteroom_fg' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
            'catatan'    => 'nullable|string',
        ]);

        $data = $request->only([
            'date', 'pukul', 'shift',
            'chillroom', 'cs_1', 'cs_2', 'anteroom_rm',
            'seasoning_suhu', 'seasoning_rh',
            'rice', 'noodle', 'prep_room', 'cooking',
            'filling', 'topping', 'packing',
            'ds_suhu', 'ds_rh',
            'cs_fg', 'anteroom_fg',
            'keterangan', 'catatan'
        ]);

        $data['username_updated'] = Auth::user()->username;
        $data['nama_produksi'] = session()->has('selected_produksi') 
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name 
        : null;

        $suhu->update($data);

        // Update tgl_update_produksi = updated_at + 1 jam
        $suhu->update(['tgl_update_produksi' => Carbon::parse($suhu->updated_at)->addHour()]);

        return redirect()->route('suhu.index')->with('success', 'Data Suhu berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');

        $data = Suhu::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produksi', 'like', "%{$search}%")
            ->orWhere('shift', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('pukul', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.suhu.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
    // Validasi input
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

    // Cari data berdasarkan UUID
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();

    // Update status dan catatan
        $suhu->status_spv = $request->status_spv;
        $suhu->catatan_spv = $request->catatan_spv;
        $suhu->nama_spv = Auth::user()->username;
        $suhu->tgl_update_spv = now();
        $suhu->save();

    // Redirect kembali dengan pesan sukses
        return redirect()->route('suhu.verification')
        ->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();
        $suhu->delete();
        return redirect()->route('suhu.verification')->with('success', 'Suhu berhasil dihapus');
    }

    public function recyclebin()
    {
        $suhu = Suhu::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.suhu.recyclebin', compact('suhu'));
    }
    public function restore($uuid)
    {
        $suhu = Suhu::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $suhu->restore();

        return redirect()->route('suhu.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $suhu = Suhu::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $suhu->forceDelete();

        return redirect()->route('suhu.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }


    public function export(Request $request)
    {
        $date = $request->input('date');

    // Load template Excel
        $templatePath = storage_path('app/templates/pemeriksaan_suhu.xlsx');
        $spreadsheet  = IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

    // Ambil data dari DB (1 hari berdasarkan date)
        $data = Suhu::whereDate('date', $date)
        ->orderBy('pukul', 'asc')
        ->get([
            'date','pukul','shift','username','nama_produksi',
            'chillroom','cs_1','cs_2','anteroom_rm',
            'seasoning_suhu','seasoning_rh',
            'rice','noodle','prep_room','cooking',
            'filling','topping','packing',
            'ds_suhu','ds_rh','cs_fg','anteroom_fg',
            'status_produksi','status_spv','catatan_spv'
        ]);

        if ($data->isNotEmpty()) {
            $first = $data->first();

        // Daftar nama hari dalam bahasa Indonesia
            $hariList = [
                'Sunday'    => 'Minggu',
                'Monday'    => 'Senin',
                'Tuesday'   => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday'  => 'Kamis',
                'Friday'    => 'Jumat',
                'Saturday'  => 'Sabtu'
            ];

            $hari    = $hariList[date('l', strtotime($first->date))];
            $tanggal = date('d-m-Y', strtotime($first->date));

        // Header Hari/Tanggal & Shift
            $sheet->setCellValue('A5', "Hari/Tanggal: {$hari}, {$tanggal}");
            $sheet->mergeCells('A5:E5');
            $sheet->setCellValue('F5', 'Shift: ' . $first->shift);
            $sheet->mergeCells('F5:H5');
            $sheet->getStyle('A5:H5')->getFont()->setBold(true);

        // Definisi standar suhu/range
            $standar = [
                'chillroom'     => ['min' => 0,  'max' => 4],
                'cs_1'          => ['min' => -22, 'max' => -18],
                'cs_2'          => ['min' => -22, 'max' => -18],
                'anteroom_rm'   => ['min' => 8,  'max' => 10],
                'seasoning_suhu'=> ['min' => 22, 'max' => 30],
                'seasoning_rh'  => ['min' => 0,  'max' => 75],
                'prep_room'     => ['min' => 8,  'max' => 15],
                'cooking'       => ['min' => 20, 'max' => 30],
                'filling'       => ['min' => 20, 'max' => 30],
                'rice'          => ['min' => 20, 'max' => 30],
                'noodle'        => ['min' => 20, 'max' => 30],
                'topping'       => ['min' => 8,  'max' => 15],
                'packing'       => ['min' => 8,  'max' => 15],
                'ds_suhu'       => ['min' => 20, 'max' => 30],
                'ds_rh'         => ['min' => 0,  'max' => 75],
                'cs_fg'         => ['min' => -22,'max' => -18],
                'anteroom_fg'   => ['min' => 0,  'max' => 10],
            ];

        // Mapping kolom
            $mapKolom = [
                'B' => 'chillroom',
                'C' => 'cs_1',
                'D' => 'cs_2',
                'E' => 'anteroom_rm',
                'F' => 'seasoning_suhu',
                'G' => 'seasoning_rh',
                'H' => 'prep_room',
                'I' => 'cooking',
                'J' => 'filling',
                'K' => 'rice',
                'L' => 'noodle',
                'M' => 'topping',
                'N' => 'packing',
                'O' => 'ds_suhu',
                'P' => 'ds_rh',
                'Q' => 'cs_fg',
                'R' => 'anteroom_fg',
            ];

        // Isi data mulai row 11
            $row = 11;
            foreach ($data as $item) {
                $sheet->setCellValue('A'.$row, date('H:i', strtotime($item->pukul)));

                foreach ($mapKolom as $col => $field) {
                    $val = $item->$field;
                    $sheet->setCellValue($col.$row, $val);

                    if ($val !== null && $val !== '') {
                        $min = $standar[$field]['min'];
                        $max = $standar[$field]['max'];

                        if ($val < $min || $val > $max) {
                            $sheet->getStyle($col.$row)->getFont()->getColor()
                            ->setARGB(Color::COLOR_RED);
                            $sheet->getStyle($col.$row)->getFont()->setBold(true);
                        }
                    }
                }

                $sheet->setCellValue('S'.$row, $item->keterangan ?? '');
                $sheet->setCellValue('T'.$row, $item->username);
                $sheet->setCellValue('U'.$row, $item->nama_produksi);

            // Rata tengah tiap baris
                $sheet->getStyle("A{$row}:U{$row}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

                $row++;
            }

        // Gabungkan semua catatan_spv jadi 1 kalimat dipisahkan koma
            $allNotes = [];
            foreach ($data as $item) {
                if (!empty($item->catatan_spv)) {
                    $allNotes[] = $item->catatan_spv;
                }
            }
            $catatanGabung = implode(', ', $allNotes);

        // Taruh di baris 37 kolom A
            $sheet->setCellValue('A37', $catatanGabung);
            $sheet->mergeCells('A37:H37');
            $sheet->getStyle('A37')->getAlignment()->setWrapText(true);
            $sheet->getStyle('A37')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_TOP);

        // Output langsung ke browser
            $filename = "pemeriksaan_suhu_{$date}.xlsx";
            if (ob_get_contents()) ob_end_clean();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } else {
        // Kalau tidak ada data
            return redirect()->back()->with('error', 'Tidak ada data pada tanggal ' . $date);
        }
    }

    public function exportPdf(Request $request)
    {
        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

    // Ambil semua data suhu untuk tanggal tertentu
        $data = Suhu::whereDate('date', $date)
        ->orderBy('pukul', 'asc')
        ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data suhu untuk tanggal ini');
        }

        $first = $data->first();

        $hariList = [
            'Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
            'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
        ];
        $hari = $hariList[date('l', strtotime($first->date))] ?? '-';
        $tanggal = date('d-m-Y', strtotime($first->date)) ?? '-';

        $pdf = new \TCPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
        $pdf->setPrintHeader(false); 
        $pdf->setPrintFooter(false); 
        $pdf->SetCreator('Sistem');
        $pdf->SetAuthor('QC System');
        $pdf->SetTitle('Pemeriksaan Suhu ' . $date);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

    // === HEADER JUDUL RESMI ===
        $pdf->SetFont('times', 'I', 7);
        $pdf->Cell(0, 3, "PT. Charoen Pokphand Indonesia", 0, 1, 'L');
        $pdf->Cell(0, 3, "Food Division", 0, 1, 'L');
        // $pdf->Ln(2);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, "PEMERIKSAAN SUHU RUANG", 0, 1, 'C');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 8, "Hari/Tanggal: {$hari}, {$tanggal} | Shift: {$first->shift}", 0, 1, 'L');
        // $pdf->Ln(2);

  // === HEADER TABEL MANUAL ===
        $pdf->SetFont('times', 'B', 8);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetTextColor(0);


// Set font judul tabel
        $pdf->SetFont('times', 'B', 9);

// === HEADER BARIS 1 ===
        $pdf->Cell(15, 15, 'Pukul', 1, 0, 'C', 1);                  // Pukul
        $pdf->Cell(257, 5, 'Ruangan (°C)', 1, 0, 'C', 1);          // Ruangan merge
        $pdf->Cell(25, 15, 'Keterangan', 1, 0, 'C', 1);             // Keterangan
        $pdf->Cell(40, 5, 'PARAF', 1, 1, 'C', 1);                  // Paraf merge

// === HEADER BARIS 2 ===
        $pdf->SetFont('times', '', 8);
        $pdf->Cell(15, 10, '', 0, 0); // spacer

        $pdf->Cell(15, 10, 'Chillroom', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Cold Stor. 1', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Cold Stor. 2', 1, 0, 'C');
        $pdf->Cell(16, 10, 'Anteroom RM', 1, 0, 'C');

// Seasoning (gabungan 2 kolom)
        $pdf->Cell(30, 5, 'Seasoning', 1, 0, 'C');

// Ruangan lain
        $pdf->Cell(15, 10, 'Prep Room', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Cooking', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Filling', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Rice', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Noodle', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Topping', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Packing', 1, 0, 'C');

// Dry Store (gabungan 2 kolom)
        $pdf->Cell(30, 5, 'Dry Store', 1, 0, 'C');

        $pdf->Cell(15, 10, 'CS FG', 1, 0, 'C');
        $pdf->Cell(16, 10, 'Anteroom FG', 1, 0, 'C');
        $pdf->Cell(25, 10, '', 0, 0, 'C'); // keterangan
        $pdf->Cell(20, 10, 'QC', 1, 0, 'C');
        $pdf->Cell(20, 10, 'PROD.', 1, 0, 'C');
        $pdf->Cell(20, 10, '', 0, 0, 'C');
        $pdf->Cell(0, 5, '', 0, 1, 'C');

// === HEADER BARIS 3 (Subkolom T/RH) ===
        $pdf->Cell(76, 5, '', 0, 0); // skip sampai Seasoning
        $pdf->Cell(15, 5, 'T (°C)', 1, 0, 'C');
        $pdf->Cell(15, 5, 'RH (%)', 1, 0, 'C');
        $pdf->Cell(105, 5, '', 0, 0); // skip ke Dry Store
        $pdf->Cell(15, 5, 'T (°C)', 1, 0, 'C');
        $pdf->Cell(15, 5, 'RH (%)', 1, 0, 'C');
        $pdf->Cell(96, 5, '', 0, 1);

// === HEADER BARIS 4 (STD °C) ===
        $pdf->SetFont('times', '', 8);
        $pdf->Cell(15, 5, 'STD (°C)', 1, 0, 'C');
        $pdf->Cell(15, 5, '0 – 4', 1, 0, 'C');
        $pdf->Cell(15, 5, '-20 ± 2', 1, 0, 'C');
        $pdf->Cell(15, 5, '-20 ± 2', 1, 0, 'C');
        $pdf->Cell(16, 5, '8 – 10', 1, 0, 'C');

// Seasoning
        $pdf->Cell(15, 5, '22 – 30', 1, 0, 'C');
        $pdf->SetFont('dejavusans', '', 7);
        $pdf->Cell(15, 5, '≤ 75%', 1, 0, 'C');
        $pdf->SetFont('times', '', 8);

// Ruangan lain
        $pdf->Cell(15, 5, '9 – 15', 1, 0, 'C');
        $pdf->Cell(15, 5, '20 – 30', 1, 0, 'C');
        $pdf->Cell(15, 5, '20 – 30', 1, 0, 'C');
        $pdf->Cell(15, 5, '20 – 30', 1, 0, 'C');
        $pdf->Cell(15, 5, '20 – 30', 1, 0, 'C');
        $pdf->Cell(15, 5, '9 – 15', 1, 0, 'C');
        $pdf->Cell(15, 5, '9 – 15', 1, 0, 'C');

// Dry Store
        $pdf->Cell(15, 5, '20 – 30', 1, 0, 'C');
        $pdf->SetFont('dejavusans', '', 7);
        $pdf->Cell(15, 5, '≤ 75%', 1, 0, 'C');
        $pdf->SetFont('times', '', 8);

// Lainnya
        $pdf->Cell(15, 5, '-19 ± 1', 1, 0, 'C');
        $pdf->Cell(16, 5, '0 – 10', 1, 0, 'C');
        $pdf->Cell(25, 5, '', 1, 0, 'C');
        $pdf->Cell(20, 5, '', 1, 0, 'C');
        $pdf->Cell(20, 5, '', 1, 1, 'C');


    // === ISI DATA ===
        $pdf->SetFont('times', '', 9);
        foreach ($data as $item) {
            $pdf->Cell(15, 5, date('H:i', strtotime($item->pukul)), 1, 0, 'C');
            $pdf->Cell(15, 5, $item->chillroom, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->cs_1, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->cs_2, 1, 0, 'C');
            $pdf->Cell(16, 5, $item->anteroom_rm, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->seasoning_suhu, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->seasoning_rh, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->prep_room, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->cooking, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->filling, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->rice, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->noodle, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->topping, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->packing, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->ds_suhu, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->ds_rh, 1, 0, 'C');
            $pdf->Cell(15, 5, $item->cs_fg, 1, 0, 'C');
            $pdf->Cell(16, 5, $item->anteroom_fg, 1, 0, 'C');
            $pdf->Cell(25, 5, $item->keterangan, 1, 0, 'C');
            $pdf->Cell(20, 5, $item->username, 1, 0, 'C');
            $pdf->Cell(20, 5, $item->nama_produksi, 1, 1, 'C');
        }

        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 02/05', 0, 1, 'R'); 

        $all_data = Suhu::whereDate('created_at', $date)->get();

        $all_notes = $all_data->pluck('catatan')->filter()->toArray();

        $notes_text = !empty($all_notes) ? implode(', ', $all_notes) : '-';

        $y_bawah = $pdf->GetY();
        $pdf->SetXY(15, $y_bawah);
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(0, 6, 'Catatan:', 0, 1);
        $pdf->SetFont('times', '', 8);
        $pdf->MultiCell(0, 5, $notes_text, 0, 'L', 0, 1);

// === TTD BARCODE ===
        $last = $data->last();
        $qc = User::where('username', $last->username)->first();
        $spv = User::where('username', $last->nama_spv ?? '')->first();
        $produksi_nama = $last->nama_produksi;

        $qc_tgl   = $last->created_at ? $last->created_at->format('d-m-Y H:i') : '-';
        $prod_tgl = $last->tgl_update_produksi ? date('d-m-Y H:i', strtotime($last->tgl_update_produksi)) : '-';
        $spv_tgl  = $last->tgl_update_spv ? date('d-m-Y H:i', strtotime($last->tgl_update_spv)) : '-';

// Ukuran dan posisi barcode
        $barcode_size = 15;
        $y_offset = 5; 
        $page_width = $pdf->getPageWidth();
        $margin = 70;                    
        $usable_width = $page_width - 2 * $margin;
        $gap = ($usable_width - 3 * $barcode_size) / 2;
        $x_positions_centered = [
            $margin,        
            $margin + $barcode_size + $gap, 
            $margin + 2*($barcode_size + $gap) 
        ];

// Tinggi area TTD (perkiraan)
        $signature_height = 60;

// Posisi sekarang
        $currentY = $pdf->GetY();

// Batas bawah halaman
        $pageHeight = $pdf->getPageHeight();
        $bottomMargin = $pdf->getBreakMargin();

// Sisa ruang
        $availableSpace = $pageHeight - $currentY - $bottomMargin;

// Kalau nggak muat → page baru
        if ($availableSpace < $signature_height) {
            $pdf->AddPage();
        }

        $y_start = $pdf->GetY();

        if ($last->status_spv == 1 && $spv) {
    // ===== QC =====
            $barcode_x = $x_positions_centered[0];
            $barcode_y = $y_start + $y_offset;
            $pdf->SetXY($barcode_x, $y_start);
            $pdf->Cell($barcode_size, 6, 'Dibuat Oleh', 0, 1, 'C');

            $qc_name = $qc?->name ?? '-';
            $qc_text = "Jabatan: QC Inspector\nNama: {$qc_name}\nTgl Dibuat: {$qc_tgl}";
            $pdf->write2DBarcode($qc_text, 'QRCODE,L', $barcode_x, $barcode_y, $barcode_size, $barcode_size, null, 'N');

            $pdf->SetXY($barcode_x, $barcode_y + $barcode_size);
            $pdf->MultiCell($barcode_size, 5, "QC Inspector", 0, 'C');

    // ===== Produksi =====
            $barcode_x = $x_positions_centered[1];
            $barcode_y = $y_start + $y_offset;
            $pdf->SetXY($barcode_x, $y_start);
            $pdf->Cell($barcode_size, 6, 'Diketahui Oleh', 0, 1, 'C');

            $prod_text = "Jabatan: Foreman/Forelady Produksi\nNama: {$produksi_nama}\nTgl Diketahui: {$prod_tgl}";
            $pdf->write2DBarcode($prod_text, 'QRCODE,L', $barcode_x, $barcode_y, $barcode_size, $barcode_size, null, 'N');

            $pdf->SetXY($barcode_x, $barcode_y + $barcode_size);
            $pdf->MultiCell($barcode_size, 5, "Foreman/ Forelady Produksi", 0, 'C');

    // ===== Supervisor =====
            $barcode_x = $x_positions_centered[2];
            $barcode_y = $y_start + $y_offset;
            $pdf->SetXY($barcode_x, $y_start);
            $pdf->Cell($barcode_size, 6, 'Disetujui Oleh', 0, 1, 'C');

            $spv_name = $spv->name ?? '-';
            $spv_text = "Jabatan: Supervisor QC\nNama: {$spv_name}\nTgl Verifikasi: {$spv_tgl}";
            $pdf->write2DBarcode($spv_text, 'QRCODE,L', $barcode_x, $barcode_y, $barcode_size, $barcode_size, null, 'N');

            $pdf->SetXY($barcode_x, $barcode_y + $barcode_size);
            $pdf->MultiCell($barcode_size, 5, "Supervisor QC", 0, 'C');

        } else {
            $pdf->SetXY($x_positions_centered[2], $y_start + 20);
            $pdf->SetFont('times', '', 10);
            $pdf->SetTextColor(255, 0, 0);
            $pdf->Cell($barcode_size, 6, 'Data belum diverifikasi', 0, 0, 'C');
            $pdf->SetTextColor(0);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output("pemeriksaan_suhu_{$date}.pdf", 'I');
        exit;
    }


}
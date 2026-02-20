<?php

namespace App\Http\Controllers;

use App\Models\Thumbling;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// excel
use App\Models\User;
use Illuminate\Support\Facades\Response;


class ThumblingController extends Controller
{ 
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $data = Thumbling::query()
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
            ->orWhere('nama_produk', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.thumbling.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('form.thumbling.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->value('name')
        : 'Produksi RTM';

        $validated = $request->validate([
        // Data umum
            'date' => 'required|date',
            'shift' => 'required|string',
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'identifikasi_daging' => 'required|string',
            'asal_daging' => 'required|string',
            'kode_daging' => 'nullable|array',
            'berat_daging' => 'nullable|array',
            'suhu_daging' => 'nullable|array',
            'rata_daging' => 'nullable|array',
            'kondisi_daging' => 'nullable|string',
            'premix' => 'nullable|array',
            'kode_premix' => 'nullable|array',
            'berat_premix' => 'nullable|array',
        // Bahan Lain
            'bahan_lain' => 'nullable|array', 
            'bahan_lain.*.premix' => 'nullable|string',
            'bahan_lain.*.kode' => 'nullable|string',
            'bahan_lain.*.berat' => 'nullable|numeric',
            'bahan_lain.*.sens' => 'nullable|string',
        // Parameter cairan
            'air' => 'nullable|numeric',
            'suhu_air' => 'nullable|numeric',
            'suhu_marinade' => 'nullable|numeric',
            'lama_pengadukan' => 'nullable|numeric',
            'marinade_brix_salinity' => 'nullable|string',
        // Parameter thumbling
            'drum_on' => 'nullable|numeric',
            'drum_off' => 'nullable|numeric',
            'drum_speed' => 'nullable|numeric',
            'vacuum_time' => 'nullable|numeric',
            'total_time' => 'nullable|numeric',
            'waktu_mulai' => 'nullable',
            'waktu_selesai' => 'nullable',
            'suhu_daging_thumbling' => 'nullable|array',
            'rata_daging_thumbling' => 'nullable|numeric',
            'kondisi_daging_akhir' => 'nullable|string',
            'catatan_akhir' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $validated['username'] = $username;
        $validated['nama_produksi'] = $nama_produksi;
        $validated['status_produksi'] = "1";
        $validated['status_spv'] = "0";
        $validated['tgl_update_produksi'] = now()->addHour();

        Thumbling::create($validated);

        return redirect()->route('thumbling.index')->with('success', 'Data Pemeriksaan Proses Thumbling berhasil disimpan');
    }

    public function edit($uuid)
    {
        $thumbling = Thumbling::where('uuid', $uuid)->firstOrFail();
        $produks   = Produk::all();

        return view('form.thumbling.edit', compact('thumbling', 'produks'));
    }

    public function update(Request $request, $uuid)
    {
        $thumbling = Thumbling::where('uuid', $uuid)->firstOrFail();

        $username_updated = Auth::user()->username ?? 'User RTM';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->value('name')
        : 'Produksi RTM';

        $validated = $request->validate([
            'date' => 'required|date',
            'shift' => 'required|string',
            'nama_produk' => 'required|string',
            'kode_produksi' => 'required|string',
            'identifikasi_daging' => 'required|string',
            'asal_daging' => 'required|string',

            'kode_daging' => 'nullable|array',
            'berat_daging' => 'nullable|array',
            'suhu_daging' => 'nullable|array',
            'rata_daging' => 'nullable|array',

            'kondisi_daging' => 'nullable|string',

            'premix' => 'nullable|array',
            'kode_premix' => 'nullable|array',
            'berat_premix' => 'nullable|array',

            'bahan_lain' => 'nullable|array',
            'bahan_lain.*.premix' => 'nullable|string',
            'bahan_lain.*.kode' => 'nullable|string',
            'bahan_lain.*.berat' => 'nullable|numeric',
            'bahan_lain.*.sens' => 'nullable|string',

            'air' => 'nullable|numeric',
            'suhu_air' => 'nullable|numeric',
            'suhu_marinade' => 'nullable|numeric',
            'lama_pengadukan' => 'nullable|numeric',
            'marinade_brix_salinity' => 'nullable|string',

            'drum_on' => 'nullable|numeric',
            'drum_off' => 'nullable|numeric',
            'drum_speed' => 'nullable|numeric',
            'vacuum_time' => 'nullable|numeric',
            'total_time' => 'nullable|numeric',

            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',

            'suhu_daging_thumbling' => 'nullable|array',
            'rata_daging_thumbling' => 'nullable|numeric',

            'kondisi_daging_akhir' => 'nullable|string',
            'catatan_akhir' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

    // ===== NORMALISASI ARRAY =====
        if (isset($validated['bahan_lain'])) {
            $validated['bahan_lain'] = array_values($validated['bahan_lain']);
        }

        $validated['kode_daging'] = $validated['kode_daging'] ?? [];
        $validated['berat_daging'] = $validated['berat_daging'] ?? [];
        $validated['suhu_daging'] = $validated['suhu_daging'] ?? [];
        $validated['rata_daging'] = $validated['rata_daging'] ?? [];

        $validated['premix'] = $validated['premix'] ?? [];
        $validated['kode_premix'] = $validated['kode_premix'] ?? [];
        $validated['berat_premix'] = $validated['berat_premix'] ?? [];

        $validated['bahan_lain'] = $validated['bahan_lain'] ?? [];
        $validated['suhu_daging_thumbling'] = $validated['suhu_daging_thumbling'] ?? [];

    // ===== META =====
        $validated['username_updated'] = $username_updated;
        $validated['nama_produksi'] = $nama_produksi;
        $validated['tgl_update_produksi'] = now()->addHour();

    // ===== SAVE =====
        $thumbling->fill($validated);
        $thumbling->save();

        return redirect()
        ->route('thumbling.index')
        ->with('success', 'Data Pemeriksaan Proses Thumbling berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search = $request->search;
        $date   = $request->date;

        $baseQuery = Thumbling::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%");
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        });

    // DATA TABEL
        $data = (clone $baseQuery)
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

    // 🔥 DROPDOWN PRODUK (PASTI SESUAI)
        $produkList = (clone $baseQuery)
        ->select('nama_produk')
        ->distinct()
        ->orderBy('nama_produk')
        ->pluck('nama_produk');

        return view(
            'form.thumbling.verification',
            compact('data', 'search', 'date', 'produkList')
        );
    }

    public function getProdukByDate(Request $request)
    {
        $date = $request->date;

        $produk = Thumbling::whereDate('date', $date)
        ->select('nama_produk')
        ->distinct()
        ->orderBy('nama_produk')
        ->pluck('nama_produk');

        return response()->json($produk);
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $thumbling = Thumbling::where('uuid', $uuid)->firstOrFail();

        $thumbling->status_spv = $request->status_spv;
        $thumbling->catatan_spv = $request->catatan_spv;
        $thumbling->nama_spv = Auth::user()->username;
        $thumbling->tgl_update_spv = now();
        $thumbling->save();

        return redirect()->route('thumbling.verification')->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $thumbling = Thumbling::where('uuid', $uuid)->firstOrFail();
        $thumbling->delete();
        return redirect()->route('thumbling.verification')->with('success', 'Thumbling berhasil dihapus');
    }

    public function recyclebin()
    {
        $thumbling = Thumbling::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.thumbling.recyclebin', compact('thumbling'));
    }
    public function restore($uuid)
    {
        $thumbling = Thumbling::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $thumbling->restore();

        return redirect()->route('thumbling.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $thumbling = Thumbling::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $thumbling->forceDelete();

        return redirect()->route('thumbling.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    } 

    public function exportPdf(Request $request)
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        if (ob_get_length()) ob_end_clean();

        require_once base_path('vendor/tecnickcom/tcpdf/tcpdf.php');

        $request->validate([
            'date' => 'required|date',
            'nama_produk' => 'required'
        ]);

        $date   = \Carbon\Carbon::parse($request->date)->format('Y-m-d');
        $produk = $request->nama_produk;

        $rows = Thumbling::whereDate('date', $date)
        ->where('nama_produk', $produk)
        ->get();

        if ($rows->isEmpty()) return back()->with('error', 'Data tidak ditemukan');

        $j = function ($val) {
            if (is_array($val)) return $val;
            if (is_string($val)) {
                $d = json_decode($val, true);
                return is_array($d) ? $d : [$val];
            }
            return [];
        };

        $hariMap = [
            'Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
            'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
        ];
        $hari = $hariMap[date('l', strtotime($date))];

        $pdf = new \TCPDF('L','mm','LEGAL',true,'UTF-8',false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(5,5,5);
        $pdf->SetAutoPageBreak(false, 10);
        $pdf->AddPage();

        /* ================= JUDUL ================= */
        $pdf->SetFont('times','B',11);
        $pdf->Cell(0,7,'PEMERIKSAAN PROSES TUMBLING',0,1,'C');

        $pdf->SetFont('times','',9);
        $pdf->Cell(90,6,"Hari/Tanggal : $hari / ".date('d-m-Y',strtotime($date)),0,0);
        $pdf->Cell(60,6,"Shift : ".(string)$rows[0]->shift,0,0);
        $pdf->Cell(0,6,"Produk : $produk",0,1);

        /* ================= HELPER FUNCTIONS ================= */
        $checkPageBreak = function($pdf, $heightNeeded) {
            $pageHeight = $pdf->getPageHeight();
            $bottomMargin = 10;
            if ($pdf->GetY() + $heightNeeded > $pageHeight - $bottomMargin) {
                $pdf->AddPage();
            }
        };

        $calcMaxHeight = function($pdf, $dataArr, $colWidth) {
            $maxHeight = 6;
            foreach ($dataArr as $val) {
                if (is_array($val)) $val = implode("\n", $val);
                $nb = $pdf->getNumLines((string)$val, $colWidth);
                $height = 6 * $nb;
                if ($height > $maxHeight) $maxHeight = $height;
            }
            return $maxHeight;
        };

        $labelBaseWidth = 45;
        $colBaseWidth   = 22;
        $maxColsPerBatch = 4;

        /* ================= IDENTIFIKASI DAGING ================= */
        $pdf->SetFont('times','B',9);
        $pdf->Cell(0,6,'IDENTIFIKASI DAGING',1,1,'L');
        $pdf->SetFont('times','',8);

    // Asal
        $checkPageBreak($pdf,6);
        $pdf->MultiCell($labelBaseWidth, 6, 'Asal',1,'L',0,0);
        foreach ($rows as $row) {
            $val = (string)$row->asal_daging;
            $maxHeight = $calcMaxHeight($pdf, [$val], $colBaseWidth*$maxColsPerBatch);
            $checkPageBreak($pdf,$maxHeight);
            $pdf->MultiCell($colBaseWidth*$maxColsPerBatch,$maxHeight,$val,1,'C',0,0);
        }
        $pdf->Ln();

    // Detail lainnya
        $details = [
            'Tanggal Produksi / Kode' => 'kode_daging',
            'Berat (kg)' => 'berat_daging',
            'Suhu Daging (°C)' => 'suhu_daging',
            'Rata-rata' => 'rata_daging'
        ];
        foreach ($details as $label => $key) {
            $dataWidth = $pdf->GetStringWidth($label)+6;
            $maxHeight = 6;
            foreach ($rows as $row) {
                $dataArr = $j($row->{$key});
                $height = $calcMaxHeight($pdf, $dataArr, $colBaseWidth);
                if ($height > $maxHeight) $maxHeight = $height;
            }
            $checkPageBreak($pdf,$maxHeight);
            $pdf->MultiCell(max($labelBaseWidth,$dataWidth), $maxHeight, $label,1,'L',0,0);
            foreach ($rows as $row) {
                $dataArr = $j($row->{$key});
                for ($i=0;$i<$maxColsPerBatch;$i++) {
                    $val = $dataArr[$i] ?? '';
                    if (is_array($val)) $val = implode("\n",$val);
                    $pdf->MultiCell($colBaseWidth,$maxHeight,$val,1,'C',0,0);
                }
            }
            $pdf->Ln();
        }

    // Kondisi Daging
        $checkPageBreak($pdf,6);
        $pdf->MultiCell($labelBaseWidth,6,'Kondisi Daging',1,'L',0,0);
        foreach ($rows as $row) {
            $val = (string)$row->kondisi_daging;
            $maxHeight = $calcMaxHeight($pdf, [$val], $colBaseWidth*$maxColsPerBatch);
            $checkPageBreak($pdf,$maxHeight);
            $pdf->MultiCell($colBaseWidth*$maxColsPerBatch,$maxHeight,$val,1,'C',0,0);
        }
        $pdf->Ln();

        /* ================= MARINADE ================= */
        $pdf->SetFont('times','B',9);
        $pdf->Cell(0,6,'MARINADE',1,1,'L');
        $pdf->SetFont('times','',8);

        $labels = ['Bahan Utama'=>'premix','Kode'=>'kode_premix','Berat (kg)'=>'berat_premix'];
        foreach ($labels as $lbl => $key) {
            $dataWidth = $pdf->GetStringWidth($lbl)+6;
            $maxHeight = 6;
            foreach ($rows as $row) {
                $dataArr = $j($row->{$key});
                $height = $calcMaxHeight($pdf, $dataArr, $colBaseWidth);
                if ($height > $maxHeight) $maxHeight = $height;
            }
            $checkPageBreak($pdf,$maxHeight);
            $pdf->MultiCell(max($labelBaseWidth,$dataWidth),$maxHeight,$lbl,1,'L',0,0);
            foreach ($rows as $row) {
                $dataArr = $j($row->{$key});
                for ($i=0;$i<$maxColsPerBatch;$i++) {
                    $val = $dataArr[$i] ?? '';
                    $pdf->MultiCell($colBaseWidth,$maxHeight,$val,1,'C',0,0);
                }
            }
            $pdf->Ln();
        }

        /* ================= BAHAN LAIN ================= */
        $pdf->SetFont('times','B',9);
        $pdf->Cell(0,6,'BAHAN LAIN YANG DITAMBAHKAN',1,1,'L');
        $pdf->SetFont('times','',8);

        $wNama = 45; $wKode = 40; $wBerat = 25; $wSens = 23;

    // Ambil semua bahan unik
        $allBahan = [];
        foreach ($rows as $row) {
            $bahan_lain = $j($row->bahan_lain);
            foreach ($bahan_lain as $b) {
                $name = $b['premix'] ?? '';
                if ($name) $allBahan[strtolower($name)] = $name;
            }
        }

        foreach ($allBahan as $key => $bahanName) {
            $cellsPerBatch = [];
            $maxLines = 1;

            foreach ($rows as $row) {
                $bahan_lain = $j($row->bahan_lain);
                $found = null;
                foreach ($bahan_lain as $b) {
                    if (strcasecmp(($b['premix'] ?? ''), $bahanName) === 0) {
                        $found = $b; break;
                    }
                }
                $kode  = $found['kode'] ?? '';
                $berat = $found['berat'] ?? '';
                $sens  = $found['sens'] ?? '';
                $lines = max(substr_count($kode,"\n")+1, substr_count($berat,"\n")+1, substr_count($sens,"\n")+1);
                if ($lines > $maxLines) $maxLines = $lines;
                $cellsPerBatch[] = ['kode'=>$kode,'berat'=>$berat,'sens'=>$sens];
            }

            $checkPageBreak($pdf,6*$maxLines);
            $pdf->MultiCell($wNama,6*$maxLines,$bahanName,1,'L',0,0);
            foreach ($cellsPerBatch as $cell) {
                $pdf->MultiCell($wKode,6*$maxLines,$cell['kode'],1,'C',0,0);
                $pdf->MultiCell($wBerat,6*$maxLines,$cell['berat'],1,'C',0,0);
                $pdf->MultiCell($wSens,6*$maxLines,$cell['sens'],1,'C',0,0);
            }
            $pdf->Ln();
        }

    // Air, Suhu Air, Suhu Marinade, Lama Pengadukan, Brix-Salinity
        $extras = [
            'Air (kg)' => 'air',
            'Suhu Air (°C)' => 'suhu_air',
            'Suhu Marinade (°C)' => 'suhu_marinade',
            'Lama pengadukan (menit)' => 'lama_pengadukan',
            'Marinade Brix - Salinity' => 'marinade_brix_salinity'
        ];
        foreach ($extras as $label => $key) {
            $checkPageBreak($pdf,6);
            $pdf->MultiCell($labelBaseWidth,6,$label,1,'L',0,0);
            foreach ($rows as $row) {
                $val = (string)$row->{$key};
                $pdf->MultiCell($colBaseWidth*$maxColsPerBatch,6,$val,1,'C',0,0);
            }
            $pdf->Ln();
        }

        /* ================= PARAMETER ================= */
        $pdf->SetFont('times','B',9);
        $pdf->Cell(0,6,'PARAMETER TUMBLING',1,1,'L');
        $pdf->SetFont('times','',8);

        $params = [
            'Drum On' => 'drum_on',
            'Drum Off' => 'drum_off',
            'Drum Speed (RPM)' => 'drum_speed',
            'Vacuum Time' => 'vacuum_time',
            'Total Time' => 'total_time' 
        ];
        foreach ($params as $lbl => $key) {
            $maxHeight = 6;
            foreach ($rows as $row) {
                $lines = substr_count((string)$row->{$key},"\n")+1;
                if ($lines*6 > $maxHeight) $maxHeight = $lines*6;
            }
            $checkPageBreak($pdf,$maxHeight);
            $pdf->MultiCell($labelBaseWidth,$maxHeight,$lbl,1,'L',0,0);
            foreach ($rows as $row) {
                $val = (string)$row->{$key};
                $pdf->MultiCell($colBaseWidth*$maxColsPerBatch,$maxHeight,$val,1,'C',0,0);
            }
            $pdf->Ln();
        }

        /* ================= HASIL ================= */
        $pdf->SetFont('times','B',9);
        $pdf->Cell(0,6,'HASIL THUMBLING',1,1,'L');
        $pdf->SetFont('times','',8);

    // Suhu Daging
        $checkPageBreak($pdf,6);
        $pdf->MultiCell($labelBaseWidth,6,'Suhu Daging (°C)',1,'L',0,0);
        foreach ($rows as $row) {
            $suhu_hasil = $j($row->suhu_daging_thumbling);
            for ($i=0;$i<$maxColsPerBatch;$i++) {
                $val = $suhu_hasil[$i] ?? '';
                if (is_array($val)) $val = implode(' / ',$val);
                $pdf->MultiCell($colBaseWidth,6,$val,1,'C',0,0);
            }
        }
        $pdf->Ln();

    // Kondisi
        $checkPageBreak($pdf,6);
        $pdf->MultiCell($labelBaseWidth,6,'Kondisi',1,'L',0,0);
        foreach ($rows as $row) $pdf->MultiCell($colBaseWidth*$maxColsPerBatch,6,(string)$row->kondisi_daging_akhir,1,'C',0,0);
        $pdf->Ln();

        /* ================= CATATAN ================= */
        $maxHeight = 0;
        foreach ($rows as $row) {
            $nb = $pdf->getNumLines((string)$row->catatan_akhir, $colBaseWidth*$maxColsPerBatch);
            $height = 6 * $nb;
            if ($height > $maxHeight) $maxHeight = $height;
        }
        $checkPageBreak($pdf,$maxHeight);
        $pdf->MultiCell($labelBaseWidth,$maxHeight,'Catatan',1,'C',0,0);
        foreach ($rows as $row) $pdf->MultiCell($colBaseWidth*$maxColsPerBatch,$maxHeight,(string)$row->catatan_akhir,1,'L',0,0);
        $pdf->Ln();

    // Pemeriksaan
        $checkPageBreak($pdf,6);
        $pdf->MultiCell($labelBaseWidth,6,'Pemeriksaan',1,'L',0,0);
        foreach ($rows as $row) {
            $text = 'QC : ' . ($row->username ?? '') . ' | PROD : ' . ($row->nama_produksi ?? '');
            $pdf->MultiCell($colBaseWidth*$maxColsPerBatch,6,$text,1,'C',0,0);
        }
        $pdf->Ln(); 
        $pdf->SetFont('times', 'I', 8);
        $pdf->Cell(330, 5, 'QR 05/01', 0, 1, 'R'); 

        $last = $rows->last();
        $qc = User::where('username',$last->username)->first();
        $spv = User::where('username',$last->nama_spv ?? '')->first();
        $qc_tgl   = $last->created_at ? $last->created_at->format('d-m-Y H:i') : '-';
        $spv_tgl  = $last->tgl_update_spv ? date('d-m-Y H:i', strtotime($last->tgl_update_spv)) : '-';

        $barcode_size = 15;
        $y_offset = 5; 
        $page_width = $pdf->getPageWidth();
        $margin = 70;                    
        $usable_width = $page_width - 2*$margin;
        $gap = ($usable_width - 3*$barcode_size)/2;
        $x_positions_centered = [$margin,$margin+$barcode_size+$gap,$margin+2*($barcode_size+$gap)];
        $y_start = $pdf->GetY();

        if($last->status_spv==1 && $spv) {
        // QC
            $pdf->SetXY($x_positions_centered[0],$y_start);
            $pdf->Cell($barcode_size,6,'Dibuat Oleh',0,1,'C');
            $pdf->write2DBarcode("QC: ".$qc?->name."\nTgl: ".$qc_tgl,'QRCODE,L',$x_positions_centered[0],$y_start+$y_offset,$barcode_size,$barcode_size,null,'N');
            $pdf->SetXY($x_positions_centered[0],$y_start+$y_offset+$barcode_size);
            $pdf->MultiCell($barcode_size,5,'QC Inspector',0,'C');

        // SPV
            $pdf->SetXY($x_positions_centered[2],$y_start);
            $pdf->Cell($barcode_size,6,'Disetujui Oleh',0,1,'C');
            $pdf->write2DBarcode("SPV: ".$spv?->name."\nTgl: ".$spv_tgl,'QRCODE,L',$x_positions_centered[2],$y_start+$y_offset,$barcode_size,$barcode_size,null,'N');
            $pdf->SetXY($x_positions_centered[2],$y_start+$y_offset+$barcode_size);
            $pdf->MultiCell($barcode_size,5,'Supervisor QC',0,'C');
        } else {
            $pdf->SetXY($x_positions_centered[2],$y_start+20);
            $pdf->SetFont('times','',10);
            $pdf->SetTextColor(255,0,0);
            $pdf->Cell($barcode_size,6,'Data belum diverifikasi',0,0,'C');
            $pdf->SetTextColor(0);
        }

        /* ================= OUTPUT PDF ================= */
        $pdf->Output('Pemeriksaan_Tumbling_'.date('d-m-Y',strtotime($date)).'.pdf','I');
        exit;
    }



}

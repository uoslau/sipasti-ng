<?php

namespace App\Http\Controllers;

use ZipArchive;
use Carbon\Carbon;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use App\Helpers\NumberToWords;
use App\Models\PetugasKegiatan;
use App\Models\WilayahTugas;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class DownloadController extends Controller
{

    public function downloadKontrak(Request $request, $slug)
    {
        Carbon::setLocale('id');

        $selectedIds = $request->query('ids') ? explode(',', $request->query('ids')) : [];

        if (empty($selectedIds)) {
            return response()->json(['error' => 'Tidak ada data yang dipilih.'], 400);
        }

        $dateString = str_replace('-', ' ', $slug);

        $months = [
            'januari'   => 'January',
            'februari'  => 'February',
            'maret'     => 'March',
            'april'     => 'April',
            'mei'       => 'May',
            'juni'      => 'June',
            'juli'      => 'July',
            'agustus'   => 'August',
            'september' => 'September',
            'oktober'   => 'October',
            'november'  => 'November',
            'desember'  => 'December',
        ];

        foreach ($months as $indonesian => $english) {
            if (strpos($dateString, $indonesian) !== false) {
                $dateString = str_replace($indonesian, $english, $dateString);
                break;
            }
        }

        $date = Carbon::createFromFormat('F Y', $dateString);

        $petugasList = PetugasKegiatan::whereIn('sktnp', $selectedIds)
            ->whereMonth('tanggal_mulai', $date->month)
            ->whereYear('tanggal_mulai', $date->year)
            ->get();

        if ($petugasList->isEmpty()) {
            return response()->json(['error' => 'Belum ada kegiatan pada bulan tersebut.'], 404);
        }

        $petugasKegiatan = [];
        foreach ($petugasList as $petugas) {
            if ($petugas->satuan === 'O-B') {
                continue;
            }
            $petugasId = $petugas->sktnp;
            if (!isset($petugasKegiatan[$petugasId])) {
                $petugasKegiatan[$petugasId] = [
                    'petugas' => $petugas->nama_mitra,
                    'kegiatan' => []
                ];
            }
            $petugasKegiatan[$petugasId]['kegiatan'][] = $petugas;
        }

        $templatePath = storage_path('app/public/template/template_kontrak.docx');

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template file not found.'], 404);
        }

        $zip = new ZipArchive();
        $zipFileName = storage_path('app/public/kontrak/Kontrak_' . str_replace('-', '_', ucwords($slug)) . '.zip');

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['error' => 'Could not create zip file.'], 500);
        }

        $wordFiles = [];

        foreach ($petugasKegiatan as $p) {
            $templateProcessor = new TemplateProcessor($templatePath);

            $currentDate    = Carbon::createFromFormat('Y-m-d', $petugasList[0]->tanggal_mulai);
            $startMonth     = $currentDate->copy()->startOfMonth();
            $kontrakDates   = ($startMonth->isStartOfYear()) ? $startMonth->nextWeekday() : $startMonth;
            $kontrakDate    = ($kontrakDates->isSaturday() || $kontrakDates->isSunday()) ? $kontrakDates->previousWeekday() : $kontrakDates;
            $tanggal        = $kontrakDate->format('d');
            $bulan          = NumberToWords::monthName($kontrakDate->format('m'));
            $tahun          = $kontrakDate->format('Y');
            $hari           = NumberToWords::dayName($kontrakDate->format('l'));

            $kegiatanDate       = Carbon::createFromFormat('Y-m-d', $petugasList[0]->tanggal_mulai);
            $tanggal_kegiatan   = $kegiatanDate->format('d');
            $bulan_kegiatan     = NumberToWords::monthName($kegiatanDate->format('m'));
            $tahun_kegiatan     = $kegiatanDate->format('Y');

            $petugasList        = $p['kegiatan'];
            $wilayah_tugas      = $petugasList[0]->wilayah_tugas;
            $sktnp              = $petugasList[0]->sktnp;

            $posisiMitra = DB::table('mitras')->where('sktnp', $sktnp)->value('posisi');

            $honorData = DB::table('wilayah_tugas')
                ->where('kode_kabupaten', $wilayah_tugas)
                ->select('honor_max', 'honor_pengolahan')
                ->first();

            if ($posisiMitra === 'Mitra Pendataan') {
                $limitHonorPetugas = $honorData->honor_max;
            } elseif ($posisiMitra === 'Mitra Pengolahan' || $posisiMitra === 'Mitra (Pendataan dan Pengolahan)') {
                $limitHonorPetugas = $honorData->honor_pengolahan;
            } else {
                $limitHonorPetugas = 0;
            }

            $total_honor            = array_sum(array_column($petugasList, 'honor'));

            if ($total_honor > $limitHonorPetugas) {
                $total_honor = $limitHonorPetugas;
            }

            $total_honor_terbilang  = NumberToWords::toWords($total_honor);

            $data = [
                'bulan_kegiatan_kapital'    => strtoupper($bulan_kegiatan),
                'tahun_kegiatan'            => $tahun_kegiatan,
                'nomor_kontrak'             => $petugasList[0]->nomor_kontrak,
                'hari'                      => $hari,
                'tanggal_terbilang'         => ucfirst(NumberToWords::toWords($tanggal)),
                'bulan'                     => $bulan,
                'tahun_terbilang'           => ucfirst(NumberToWords::toWords($tahun)),
                'nama_mitra'                => ucwords(strtolower($p['petugas'])),
                'pekerjaan'                 => $petugasList[0]->pekerjaan,
                'alamat'                    => $petugasList[0]->alamat,
                'bulan_kegiatan'            => $bulan_kegiatan,
                'total_honor'               => number_format($total_honor, 0, ',', '.'),
                'total_honor_terbilang'     => ucfirst($total_honor_terbilang),
            ];

            foreach ($data as $key => $value) {
                $templateProcessor->setValue($key, $value);
            }

            $templateProcessor->cloneRow('nama_kegiatan', count($petugasList));
            foreach ($petugasList as $index => $k) {
                $rowIndex = $index + 1;
                $templateProcessor->setValue("no#$rowIndex", $rowIndex);
                $templateProcessor->setValue("nama_kegiatan#$rowIndex", $k->kegiatan->nama_kegiatan);
                $templateProcessor->setValue("tanggal_mulai#$rowIndex", $k['tanggal_mulai']);
                $templateProcessor->setValue("tanggal_selesai#$rowIndex", $k['tanggal_selesai']);
                $templateProcessor->setValue("beban#$rowIndex", $k['beban']);
                $templateProcessor->setValue("satuan#$rowIndex", $k['satuan']);
                $templateProcessor->setValue("honor#$rowIndex", number_format($k['honor'], 0, ',', '.'));
                $templateProcessor->setValue("mata_anggaran#$rowIndex", $k->kegiatan->mata_anggaran);
            }
            $outputPath = storage_path('app/public/bast/KONTRAK_' . str_replace(' ', '_', $data['nama_mitra']) . '.docx');
            $templateProcessor->saveAs($outputPath);
            $zip->addFile($outputPath, 'KONTRAK_' . str_replace(' ', '_', $data['nama_mitra']) . '.docx');
            $wordFiles[] = $outputPath;
        }

        $zip->close();

        foreach ($wordFiles as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }

    public function downloadBAST($slug)
    {
        $kegiatan = Kegiatan::where('slug', $slug)->with('petugasKegiatan')->first();


        if (!$kegiatan) {
            return response()->json(['error' => 'Kegiatan tidak ditemukan.'], 404);
        }

        $petugasList = $kegiatan->petugasKegiatan;

        if ($petugasList->isEmpty()) {
            return response()->json(['error' => 'Belum ada petugas pada kegiatan.'], 404);
        }

        $templatePath = storage_path('app/public/template/template_bast.docx');

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template file not found.'], 404);
        }

        $zip = new ZipArchive();
        $namaKegiatan = $petugasList->first()->kegiatan->slug;
        $zipFileName = storage_path('app/public/bast/BAST_' . str_replace(' ', '_', $namaKegiatan) . '.zip');

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['error' => 'Could not create zip file.'], 500);
        }

        $wordFiles = [];
        foreach ($petugasList as $petugas) {
            $templateProcessor = new TemplateProcessor($templatePath);

            $kontrakDate        = Carbon::createFromFormat('Y-m-d', $petugas->kegiatan->tanggal_mulai);
            $kontrakDate        = $kontrakDate->copy()->startOfMonth();
            $kontrakDate        = ($kontrakDate->isSaturday() || $kontrakDate->isSunday()) ? $kontrakDate->previousWeekday() : $kontrakDate->copy()->startOfMonth();
            $tanggal_kontrak    = $kontrakDate->format('d');
            $bulan_kontrak      = NumberToWords::monthName($kontrakDate->format('m'));
            $tahun_kontrak      = $kontrakDate->format('Y');

            $kegiatanDate       = Carbon::createFromFormat('Y-m-d', $petugas->kegiatan->tanggal_mulai);
            $tanggal_kegiatan   = $kegiatanDate->format('d');
            $bulan_kegiatan     = NumberToWords::monthName($kegiatanDate->format('m'));
            $tahun_kegiatan     = $kegiatanDate->format('Y');

            $kegiatanEndDate    = Carbon::createFromFormat('Y-m-d', $petugas->kegiatan->tanggal_selesai);

            $hari_bast              = NumberToWords::dayName($kegiatanEndDate->format('l'));
            $tanggal_bast           = $kegiatanEndDate->format('d');
            $tanggal_bast_terbilang = NumberToWords::toWords($tanggal_bast);
            $bulan_bast             = NumberToWords::monthName($kegiatanEndDate->format('m'));
            $tahun_bast             = $kegiatanEndDate->format('Y');
            $tahun_bast_terbilang   = NumberToWords::toWords($tahun_bast);

            $data = [
                'bulan_kegiatan_kapital'    => strtoupper($bulan_kegiatan),
                'tahun_kegiatan'            => $tahun_kegiatan,
                'nomor_bast'                => $petugas->nomor_bast,
                'hari'                      => $hari_bast,
                'tanggal_terbilang'         => ucfirst($tanggal_bast_terbilang),
                'bulan'                     => $bulan_bast,
                'tahun_terbilang'           => ucfirst($tahun_bast_terbilang),
                'nama_mitra'                => ucwords(strtolower($petugas->nama_mitra)),
                'alamat'                    => $petugas->alamat,
                'bulan_kegiatan'            => $bulan_kegiatan,
                'tanggal_kegiatan'          => $tanggal_kegiatan,
                'nomor_kontrak'             => $petugas->nomor_kontrak,
                'tanggal_kontrak'           => $tanggal_kontrak,
                'bulan_kontrak'             => $bulan_kontrak,
                'tahun_kontrak'             => $tahun_kontrak,
                'nama_kegiatan'             => $petugas->kegiatan->nama_kegiatan,
                'beban'                     => $petugas->beban,
                'satuan'                    => $petugas->satuan,
                // 'fungsi'                    => $petugas->kegiatan->fungsi->fungsi,
                'tim_kerja'                 => $petugas->kegiatan->timkerja->tim_kerja_alias,
            ];

            foreach ($data as $key => $value) {
                $templateProcessor->setValue($key, $value);
            }

            $outputPath = storage_path('app/public/bast/BAST_' . str_replace(' ', '_', $data['nama_mitra']) . '.docx');
            $templateProcessor->saveAs($outputPath);
            $zip->addFile($outputPath, 'BAST_' . str_replace(' ', '_', $data['nama_mitra']) . '.docx');
            $wordFiles[] = $outputPath;
        }

        $zip->close();

        foreach ($wordFiles as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }
}

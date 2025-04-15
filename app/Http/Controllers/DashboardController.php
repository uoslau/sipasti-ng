<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;
use App\Models\PetugasKegiatan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $nama_bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $bulan_sekarang = $request->input('bulan', date('n'));
        $tahun_sekarang = $request->input('tahun', date('Y'));

        if (!array_key_exists($bulan_sekarang, $nama_bulan)) {
            abort(400, 'Bulan tidak valid.');
        }

        $tahun_mulai = date('Y') - 1;
        $tahun_range = range($tahun_mulai, date('Y'));

        $slug = strtolower($nama_bulan[$bulan_sekarang]) . '-' . $tahun_sekarang;

        $kegiatan = Kegiatan::with(['petugasKegiatan.kegiatan'])
            ->whereMonth('tanggal_mulai', $bulan_sekarang)
            ->whereYear('tanggal_mulai', $tahun_sekarang)
            ->get();

        $sktnpList = $kegiatan->flatMap(function ($item) {
            return $item->petugasKegiatan;
        })->pluck('sktnp')->unique();

        $mitraInfo = DB::table('mitras')
            ->whereIn('sktnp', $sktnpList)
            ->pluck('posisi', 'sktnp');

        $limitHonor = DB::table('wilayah_tugas')
            ->select('kode_kabupaten', 'honor_max', 'honor_pengolahan')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->kode_kabupaten => [
                    'honor_max' => $item->honor_max,
                    'honor_pengolahan' => $item->honor_pengolahan
                ]];
            });

        $totalHonorPerPetugas = $kegiatan->flatMap(function ($item) {
            return $item->petugasKegiatan;
        })->groupBy('sktnp')->map(function ($items, $sktnp) use ($limitHonor, $mitraInfo) {
            $now = Carbon::now();
            $wilayahTugas = $items->first()->wilayah_tugas;
            $posisiMitra = $mitraInfo->get($sktnp, 'Tidak Diketahui');
            $honorData = $limitHonor->get($wilayahTugas, ['honor_max' => 0, 'honor_pengolahan' => 0]);

            if ($posisiMitra === 'Mitra Pendataan') {
                $honorMax = $honorData['honor_max'];
            } elseif ($posisiMitra === 'Mitra Pengolahan' || $posisiMitra === 'Mitra (Pendataan dan Pengolahan)') {
                $honorMax = $honorData['honor_pengolahan'];
            } else {
                $honorMax = 0;
            }

            $kegiatanList = $items->map(function ($petugas) use ($now) {
                $tanggalMulai = $petugas->kegiatan->tanggal_mulai;
                $tanggalSelesai = $petugas->kegiatan->tanggal_selesai;

                if ($now->lt($tanggalMulai)) {
                    $status = 'Belum Mulai';
                } elseif ($now->between($tanggalMulai, $tanggalSelesai)) {
                    $status = 'Sedang Berjalan';
                } else {
                    $status = 'Selesai';
                }

                return [
                    'nama_kegiatan'     => $petugas->kegiatan->nama_kegiatan,
                    'tanggal_mulai'     => $tanggalMulai,
                    'tanggal_selesai'   => $tanggalSelesai,
                    'status'            => $status,
                ];
            })->unique('nama_kegiatan')->values();

            return [
                'sktnp'         => $sktnp,
                'nama_mitra'    => $items->first()->nama_mitra,
                'total_honor'   => $items->sum('honor'),
                'wilayah_tugas' => $wilayahTugas,
                'honor_max'     => $honorMax,
                'kegiatan'      => $kegiatanList,
            ];
        })->sortByDesc('total_honor')->values();

        return view('dashboard.index', [
            'nama_bulan'        => $nama_bulan,
            'bulan_sekarang'    => $bulan_sekarang,
            'tahun_sekarang'    => $tahun_sekarang,
            'tahun_range'       => $tahun_range,
            'petugas_bulan'     => $totalHonorPerPetugas,
            'slug'              => $slug,
            'kegiatan'          => $kegiatan,
        ]);
    }
}

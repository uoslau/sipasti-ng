<?php

namespace App\Imports;

use App\Models\Mitra;
use App\Models\Kegiatan;
use App\Models\NomorKontrak;
use App\Models\PetugasKegiatan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PetugasImport implements ToCollection
{
    /**
     * @param Collection $collection
     */

    protected $kegiatan_id;

    public function __construct($kegiatan_id)
    {
        $this->kegiatan_id = $kegiatan_id;
    }

    public function collection(Collection $collection)
    {
        $kegiatan = Kegiatan::find($this->kegiatan_id);

        $kegiatan_id            = $kegiatan->id;
        $slug                   = $kegiatan->slug;
        $tanggal_mulai_mitra    = $kegiatan->tanggal_mulai;
        $tanggal_selesai_mitra  = $kegiatan->tanggal_selesai;
        $honor_nias             = $kegiatan->honor_nias ?? 0;
        $honor_nias_barat       = $kegiatan->honor_nias_barat ?? 0;

        $tahun = date('Y', strtotime($kegiatan->tanggal_mulai));
        $bulan = date('m', strtotime($kegiatan->tanggal_mulai));

        $global_kontrak = NomorKontrak::where('tahun', $tahun)
            ->orderBy('last_global_kontrak', 'desc')
            ->first();

        $last_global_kontrak = $global_kontrak ? $global_kontrak->last_global_kontrak : 0;

        $global_bast = NomorKontrak::where('tahun', $tahun)
            ->orderBy('last_bast', 'desc')
            ->first();

        $last_global_bast = $global_bast ? $global_bast->last_bast : 0;

        $indexKe = 1;
        $processedSktnp = [];
        foreach ($collection as $row) {
            if ($indexKe > 1) {
                if (empty($row[1]) && empty($row[2])) {
                    $indexKe++;
                    continue;
                }

                $sktnp = !empty($row[1]) ? $row[1] : '';

                if (empty($sktnp)) {
                    $indexKe++;
                    continue;
                }

                if (in_array($sktnp, $processedSktnp)) {
                    throw new \Exception("Terdapat NIK yang sama di file excel: $sktnp pada baris ke-$indexKe");
                }

                $processedSktnp[] = $sktnp;

                $cekMitra = PetugasKegiatan::where('kegiatan_id', $kegiatan_id)
                    ->where('sktnp', $sktnp)
                    ->first();

                if ($cekMitra) {
                    return redirect('/kegiatan/' . $slug)
                        ->with('error',  ucwords(strtolower($cekMitra->nama_mitra)) . ' sudah terdaftar di kegiatan ini! Silahkan cek File Excel yang diimport!');
                }

                $mitra = Mitra::where('sktnp', $sktnp)->first();

                $nama_mitra         = $mitra->nama_mitra;
                $bertugas_sebagai   = !empty($row[3]) ? $row[3] : '';

                if (!($row[4] == '1201' || $row[4] == '1225')) {
                    return redirect('/kegiatan/' . $slug)
                        ->with('error', 'Terdapat kesalahan, pastikan wilayah_tugas di file excel bernilai 1201 atau 1225');
                }

                $wilayah_tugas      = !empty($row[4]) ? $row[4] : '';
                $beban              = !empty($row[5]) ? $row[5] : '';
                $satuan             = !empty($row[6]) ? $row[6] : '';
                $honor              = ($wilayah_tugas == "1201") ? ($honor_nias * (int)$beban) : ($wilayah_tugas == "1225" ? ($honor_nias_barat * (int)$beban) : 0);
                $alamat             = $mitra->alamat;
                $pekerjaan          = $mitra->pekerjaan;

                $cek_kontrak = NomorKontrak::where('sktnp', $sktnp)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->first();

                if (!$cek_kontrak) {
                    $last_global_kontrak++;
                    $nomor_kontrak = str_pad($last_global_kontrak, 3, '0', STR_PAD_LEFT) . "/1201_MITRA/" . $tahun;

                    $kontrak_mitra = NomorKontrak::create([
                        'sktnp'                 => $sktnp,
                        'tahun'                 => $tahun,
                        'bulan'                 => $bulan,
                        'last_kontrak'          => $last_global_kontrak,
                        'last_global_kontrak'   => $last_global_kontrak,
                        'last_bast'             => $last_global_bast,
                    ]);
                } else {
                    $nomor_kontrak = str_pad($cek_kontrak->last_kontrak, 3, '0', STR_PAD_LEFT) . "/1201_MITRA/" . $tahun;
                    $kontrak_mitra = $cek_kontrak;
                }

                $last_global_bast++;
                $nomor_bast = str_pad($last_global_bast, 3, '0', STR_PAD_LEFT) . "/1201_BAST/" . $tahun;

                $kontrak_mitra->last_bast = $last_global_bast;
                $kontrak_mitra->save();

                $data = [
                    'sktnp'             => $sktnp,
                    'nama_mitra'        => $nama_mitra,
                    'kegiatan_id'       => $kegiatan_id,
                    'bertugas_sebagai'  => $bertugas_sebagai,
                    'wilayah_tugas'     => $wilayah_tugas,
                    'beban'             => (int)$beban,
                    'satuan'            => $satuan,
                    'honor'             => $honor,
                    'tanggal_mulai'     => $tanggal_mulai_mitra,
                    'tanggal_selesai'   => $tanggal_selesai_mitra,
                    'alamat'            => $alamat,
                    'pekerjaan'         => $pekerjaan,
                    'nomor_kontrak'     => $nomor_kontrak,
                    'nomor_bast'        => $nomor_bast,
                ];
                PetugasKegiatan::create($data);
            }
            $indexKe++;
        }
        return redirect('/kegiatan/' . $slug)->with('success', 'Petugas berhasil diimport!');
    }
}

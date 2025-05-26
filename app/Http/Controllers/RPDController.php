<?php

namespace App\Http\Controllers;

use App\Models\RPD;
use Illuminate\Http\Request;
use Symfony\Contracts\Service\Attribute\Required;

class RPDController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd($request);
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

        function getWarnaDeviasi($target, $realisasi)
        {
            $warna = 'bg-label-success';
            $selisih = $target - $realisasi;
            $deviasi = 0;

            if ($target != 0) {
                $deviasi = ($selisih / $target) * 100;

                if ($deviasi >= -5 && $deviasi <= 5) {
                    $warna = 'bg-label-success';
                } elseif (
                    ($deviasi > 5 && $deviasi <= 50) ||
                    ($deviasi < -5 && $deviasi >= -50)
                ) {
                    $warna = 'bg-label-warning';
                } elseif ($deviasi > 50 || $deviasi < -50) {
                    $warna = 'bg-label-danger';
                }
            }

            return [
                'selisih' => $selisih,
                'deviasi' => $deviasi,
                'warna' => $warna,
            ];
        }

        $rpd = RPD::where('bulan', $bulan_sekarang)
            ->where('tahun', $tahun_sekarang)
            ->get();

        $list_rpd = $rpd->map(function ($item) {
            $hasil = getWarnaDeviasi($item->target, $item->realisasi);

            $item->selisih = $hasil['selisih'];
            $item->deviasi = $hasil['deviasi'];
            $item->warna = $hasil['warna'];

            return $item;
        });

        $grup_belanja = $rpd->groupBy('jenis_belanja');

        $rekap_jenis_belanja = $grup_belanja->map(function ($items, $jenis) {
            return [
                'jenis_belanja' => $jenis,
                'total_target' => $items->sum('target'),
                'total_realisasi' => $items->sum('realisasi'),
            ];
        });

        $list_jenis_belanja = $rekap_jenis_belanja->map(function ($item) {
            $hasil = getWarnaDeviasi($item['total_target'], $item['total_realisasi']);

            $item['selisih'] = $hasil['selisih'];
            $item['deviasi'] = $hasil['deviasi'];
            $item['warna'] = $hasil['warna'];

            return $item;
        });

        $total_target = $rpd->sum('target');
        $total_realisasi = $rpd->sum('realisasi');

        $hasil_total = getWarnaDeviasi($total_target, $total_realisasi);

        $rekap_total = (object) [
            'total_target' => $total_target,
            'total_realisasi' => $total_realisasi,
            'selisih' => $hasil_total['selisih'],
            'deviasi' => $hasil_total['deviasi'],
            'warna' => $hasil_total['warna'],
        ];

        return view('rpd.index', [
            'nama_bulan'            => $nama_bulan,
            'bulan_sekarang'        => $bulan_sekarang,
            'tahun_sekarang'        => $tahun_sekarang,
            'tahun_range'           => $tahun_range,
            'list_rpd'              => $list_rpd,
            'rekap_jenis_belanja'   => $list_jenis_belanja,
            'rekap_total'           => $rekap_total,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'rpd_target' => $request->rpd_target ? str_replace('.', '', $request->rpd_target) : null,
            'rpd_realisasi' => $request->rpd_realisasi ? str_replace('.', '', $request->rpd_realisasi) : null,
        ]);

        $validatedData = $request->validate([
            'rpd_kegiatan'      => 'required|string|max:255',
            'rpd_jenis_belanja' => 'required|string|max:255',
            'rpd_output'        => 'required|string|max:255',
            'rpd_target'        => 'nullable|integer',
            'rpd_realisasi'     => 'nullable|integer',
            'rpd_pic'           => 'required|string|max:255',
            'rpd_bulan'         => 'required|integer',
            'rpd_tahun'         => 'required|integer',
            'rpd_catatan'       => 'nullable|string|max:1000'
        ]);

        RPD::create([
            'kegiatan'      => $validatedData['rpd_kegiatan'],
            'jenis_belanja' => $validatedData['rpd_jenis_belanja'],
            'output'        => $validatedData['rpd_output'],
            'target'        => $validatedData['rpd_target'],
            'realisasi'     => $validatedData['rpd_realisasi'],
            'pic'           => $validatedData['rpd_pic'],
            'bulan'         => $validatedData['rpd_bulan'],
            'tahun'         => $validatedData['rpd_tahun'],
            'catatan'       => $validatedData['rpd_catatan'],
        ]);

        return redirect('/monitoring_rpd')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request);
        $request->merge([
            'rpd_target' => $request->rpd_target ? str_replace('.', '', $request->rpd_target) : null,
            'rpd_realisasi' => $request->rpd_realisasi ? str_replace('.', '', $request->rpd_realisasi) : null,
        ]);

        $validatedData = $request->validate([
            'rpd_kegiatan'      => 'required|string|max:255',
            'rpd_jenis_belanja' => 'required|string|max:255',
            'rpd_output'        => 'required|string|max:255',
            'rpd_target'        => 'nullable|integer',
            'rpd_realisasi'     => 'nullable|integer',
            'rpd_pic'           => 'required|string|max:255',
            'rpd_bulan'         => 'required|integer',
            'rpd_tahun'         => 'required|integer',
            'rpd_catatan'       => 'nullable|string|max:1000',
        ]);

        $bulan = $validatedData['rpd_bulan'];
        $tahun = $validatedData['rpd_tahun'];

        RPD::where('id', $id)
            ->update([
                'kegiatan'      => $validatedData['rpd_kegiatan'],
                'jenis_belanja' => $validatedData['rpd_jenis_belanja'],
                'output'        => $validatedData['rpd_output'],
                'target'        => $validatedData['rpd_target'],
                'realisasi'     => $validatedData['rpd_realisasi'],
                'pic'           => $validatedData['rpd_pic'],
                'bulan'         => $validatedData['rpd_bulan'],
                'tahun'         => $validatedData['rpd_tahun'],
                'catatan'       => $validatedData['rpd_catatan'],
            ]);

        return redirect('/monitoring_rpd?bulan=' . $bulan . '&tahun=' . $tahun)->with('success', 'Data berhasil diedit!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

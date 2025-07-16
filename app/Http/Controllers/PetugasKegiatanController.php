<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Kegiatan;
use Illuminate\Support\Str;
use App\Models\NomorKontrak;
use App\Models\WilayahTugas;
use Illuminate\Http\Request;
use App\Imports\PetugasImport;
use App\Models\PetugasKegiatan;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class PetugasKegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Kegiatan $kegiatan)
    {
        return view('petugas.create', [
            'mitra' => Mitra::all(),
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('q');
        $mitra = Mitra::where('nama_mitra', 'LIKE', '%' . $search . '%')->take(5)->get(['sktnp', 'nama_mitra']);

        return response()->json($mitra);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('petugas.create');
    }

    public function import(Request $request)
    {
        $slug = $request->input('slug');
        $kegiatan = Kegiatan::where('slug', $slug)->first();
        $kegiatan_id = $kegiatan->id;
        Excel::import(new PetugasImport($kegiatan_id), $request->file('excel_file'));

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $slug)
    {
        $sktnp = $request['sktnp'];
        $mitra = Mitra::where('sktnp', $sktnp)->first();
        $kegiatan = Kegiatan::where('slug', $slug)->first();

        $validator = Validator::make($request->all(), [
            'bertugas_sebagai'  => 'required|string|max:255|regex:/^[a-zA-Z ]+$/',
            'wilayah_tugas'     => 'required',
            'beban'             => 'required|integer',
            'satuan'            => 'required|string|max:255|regex:/^[a-zA-Z ]+$/',
        ], [
            'bertugas_sebagai.regex' => 'Kolom ini hanya boleh berisi huruf.',
            'satuan.regex' => 'Kolom ini hanya boleh berisi huruf.',
        ]);

        if ($validator->fails()) {
            return redirect('/kegiatan/' . $slug)
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Petugas gagal ditambahkan! Periksa input data Anda.');
        }

        $validatedData = $validator->validated();

        $cek_mitra = PetugasKegiatan::where('kegiatan_id', $kegiatan->id)
            ->where('sktnp', $sktnp)
            ->first();

        if ($cek_mitra) {
            return redirect('/kegiatan/' . $slug)
                ->with('error',  ucwords(strtolower($cek_mitra->nama_mitra)) . ' sudah terdaftar di kegiatan ini!');
        }

        if ($validatedData['wilayah_tugas'] == 1) {
            $validatedData['wilayah_tugas'] = "1201";
            $honor_kegiatan = $kegiatan['honor_nias'];
        } else {
            $validatedData['wilayah_tugas'] = "1225";
            $honor_kegiatan = $kegiatan['honor_nias_barat'];
        }

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

        PetugasKegiatan::create([
            'sktnp'             => $mitra['sktnp'],
            'nama_mitra'        => $mitra['nama_mitra'],
            'kegiatan_id'       => $kegiatan['id'],
            'bertugas_sebagai'  => $validatedData['bertugas_sebagai'],
            'wilayah_tugas'     => $validatedData['wilayah_tugas'],
            'beban'             => $validatedData['beban'],
            'satuan'            => $validatedData['satuan'],
            'honor'             => $validatedData['beban'] * $honor_kegiatan,
            'tanggal_mulai'     => $kegiatan['tanggal_mulai'],
            'tanggal_selesai'   => $kegiatan['tanggal_selesai'],
            'alamat'            => $mitra['alamat'],
            'pekerjaan'         => $mitra['pekerjaan'],
            'nomor_kontrak'     => $nomor_kontrak,
            'nomor_bast'        => $nomor_bast,
        ]);

        return redirect('/kegiatan/' . $slug)->with('success', 'Petugas berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PetugasKegiatan $petugasKegiatan)
    {
        $petugasKegiatan = PetugasKegiatan::with(['kegiatan'])
            ->select('nama_mitra', 'kegiatan_id', 'nomor_kontrak', 'nomor_bast')
            ->orderBy('id', 'desc')
            ->paginate(12);
        return view('petugas.show', [
            'petugas' => $petugasKegiatan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($slug, $id)
    {
        $kegiatan = Kegiatan::where('slug', $slug)->first();

        $petugasKegiatan = PetugasKegiatan::where('kegiatan_id', $kegiatan->id)
            ->where('id', $id)
            ->first();

        return view('petugas.edit', [
            'kegiatan'          => $kegiatan,
            'petugas'           => $petugasKegiatan,
            'wilayah_tugas'     => WilayahTugas::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kegiatan $kegiatan, PetugasKegiatan $petugasKegiatan)
    {
        $validator = Validator::make($request->all(), [
            'bertugas_sebagai'  => 'required|string|max:255|regex:/^[a-zA-Z ]+$/',
            'wilayah_tugas'     => 'required',
            'beban'             => 'required|integer',
            'satuan'            => 'required|string|max:255|regex:/^[a-zA-Z ]+$/',
        ]);

        if ($validator->fails()) {
            return redirect('/kegiatan/' . $kegiatan->slug)
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Petugas gagal diedit! Periksa input data Anda.');
        }

        $validatedData = $validator->validated();

        if ($validatedData['wilayah_tugas'] == 1201) {
            $validatedData['wilayah_tugas'] = "1201";
            $honor_kegiatan = $kegiatan['honor_nias'];
        } else {
            $validatedData['wilayah_tugas'] = "1225";
            $honor_kegiatan = $kegiatan['honor_nias_barat'];
        }

        PetugasKegiatan::where('id', $petugasKegiatan->id)
            ->update([
                'bertugas_sebagai'  => $validatedData['bertugas_sebagai'],
                'wilayah_tugas'     => $validatedData['wilayah_tugas'],
                'beban'             => $validatedData['beban'],
                'satuan'            => $validatedData['satuan'],
                'honor'             => $validatedData['beban'] * $honor_kegiatan,
            ]);

        return redirect('/kegiatan/' . $kegiatan->slug)->with('success', 'Petugas berhasil diedit!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kegiatan $kegiatan, $sktnp)
    {
        $petugas = PetugasKegiatan::where('kegiatan_id', $kegiatan->id)
            ->where('sktnp', $sktnp)
            ->first();

        if ($petugas) {
            $petugas->delete();
            return redirect()->back()->with('success', ucwords(strtolower($petugas->nama_mitra)) . ' berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'Petugas tidak ditemukan.');
    }
}

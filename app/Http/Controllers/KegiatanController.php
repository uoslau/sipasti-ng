<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Fungsi;
use App\Models\Kegiatan;
use Illuminate\Support\Str;
use App\Models\MataAnggaran;
use App\Models\WilayahTugas;
use Illuminate\Http\Request;
use App\Models\PetugasKegiatan;
use App\Models\TimKerja;
use Illuminate\Support\Facades\DB;

class KegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kegiatan = Kegiatan::with(['PetugasKegiatan', 'Fungsi', 'TimKerja'])
            ->select('nama_kegiatan', 'slug', 'tanggal_mulai', 'tanggal_selesai', 'fungsi_id', 'tim_kerja_id')
            ->withSum('PetugasKegiatan', 'honor')
            ->orderBy('id', 'desc')
            ->paginate(12);

        return view('kegiatan.index', [
            'kegiatan'      => $kegiatan,
            'tim_kerja'     => TimKerja::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kegiatan.createview', [
            'fungsi'        => Fungsi::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'honor_nias' => $request->honor_nias ? str_replace('.', '', $request->honor_nias) : null,
            'honor_nias_barat' => $request->honor_nias_barat ? str_replace('.', '', $request->honor_nias_barat) : null,
        ]);

        $validatedData = $request->validate([
            'nama_kegiatan'     => 'required|string|max:255',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date',
            'mata_anggaran'     => 'required|string|max:255',
            'tim_kerja_id'      => 'required',
            'honor_nias'        => 'nullable|integer',
            'honor_nias_barat'  => 'nullable|integer',
        ]);

        $slug = Str::slug($request->nama_kegiatan);
        $count = Kegiatan::where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) {
            $slug = "{$slug}-{$count}";
        }

        Kegiatan::create([
            'nama_kegiatan'     => $validatedData['nama_kegiatan'],
            'slug'              => $slug,
            'tanggal_mulai'     => $validatedData['tanggal_mulai'],
            'tanggal_selesai'   => $validatedData['tanggal_selesai'],
            'mata_anggaran'     => $validatedData['mata_anggaran'],
            'tim_kerja_id'      => $validatedData['tim_kerja_id'],
            'honor_nias'        => $validatedData['honor_nias'] ?? 0,
            'honor_nias_barat'  => $validatedData['honor_nias_barat'] ?? 0,
        ]);

        return redirect('/kegiatan')->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kegiatan $kegiatan)
    {
        $petugasKegiatan = $kegiatan->PetugasKegiatan()->orderBy('nama_mitra', 'asc')->paginate(12);
        return view('kegiatan.show', [
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'slug'          => $kegiatan->slug,
            'petugas'       => $petugasKegiatan,
            'wilayah_tugas' => WilayahTugas::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kegiatan $kegiatan)
    {
        return view('kegiatan.edit', [
            'kegiatan'      => $kegiatan,
            'tim_kerja'     => TimKerja::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        $request->merge([
            'honor_nias' => $request->honor_nias ? str_replace('.', '', $request->honor_nias) : null,
            'honor_nias_barat' => $request->honor_nias_barat ? str_replace('.', '', $request->honor_nias_barat) : null,
        ]);

        $rules = [
            'nama_kegiatan'     => 'required|string|max:255',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date',
            'mata_anggaran'     => 'required|string|max:255',
            'tim_kerja_id'      => 'required',
            'honor_nias'        => 'nullable|integer',
            'honor_nias_barat'  => 'nullable|integer',
        ];

        $slug = Str::slug($request->nama_kegiatan);
        $count = Kegiatan::where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) {
            $slug = "{$slug}-{$count}";
        }

        $validatedData = $request->validate($rules);

        Kegiatan::where('id', $kegiatan->id)
            ->update([
                'nama_kegiatan'     => $validatedData['nama_kegiatan'],
                'slug'              => $slug,
                'tanggal_mulai'     => $validatedData['tanggal_mulai'],
                'tanggal_selesai'   => $validatedData['tanggal_selesai'],
                'mata_anggaran'     => $validatedData['mata_anggaran'],
                'tim_kerja_id'      => $validatedData['tim_kerja_id'],
                'honor_nias'        => $validatedData['honor_nias'] ?? 0,
                'honor_nias_barat'  => $validatedData['honor_nias_barat'] ?? 0,
            ]);

        PetugasKegiatan::where('kegiatan_id', $kegiatan->id)
            ->where('wilayah_tugas', '1201')
            ->update([
                'honor' => DB::raw('beban * ' . ($validatedData['honor_nias'] ?? 0))
            ]);

        PetugasKegiatan::where('kegiatan_id', $kegiatan->id)
            ->where('wilayah_tugas', '1225')
            ->update([
                'honor' => DB::raw('beban * ' . ($validatedData['honor_nias_barat'] ?? 0))
            ]);

        PetugasKegiatan::where('kegiatan_id', $kegiatan->id)
            ->update([
                'tanggal_mulai'     => $validatedData['tanggal_mulai'],
                'tanggal_selesai'   => $validatedData['tanggal_selesai']
            ]);

        return redirect('/kegiatan')->with('success', 'Kegiatan berhasil diedit!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kegiatan $kegiatan)
    {
        $kegiatan->petugasKegiatan()->delete();
        $kegiatan->delete();

        return redirect()->back()->with('success', 'Kegiatan berhasil dihapus!');
    }
}

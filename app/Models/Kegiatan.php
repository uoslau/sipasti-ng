<?php

namespace App\Models;

use App\Models\PetugasKegiatan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kegiatan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function PetugasKegiatan()
    {
        return $this->hasMany(PetugasKegiatan::class);
    }

    public function Fungsi()
    {
        return $this->belongsTo(Fungsi::class, 'fungsi_id');
    }

    public function WilayahTugas()
    {
        return $this->hasMany(WilayahTugas::class);
    }

    public function mataAnggaran()
    {
        return $this->belongsTo(MataAnggaran::class, 'mata_anggaran_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
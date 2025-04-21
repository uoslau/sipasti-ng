<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RPD extends Model
{
    protected $fillable = [
        'kegiatan',
        'jenis_belanja',
        'output',
        'target',
        'realisasi',
        'pic',
        'bulan',
        'tahun',
        'catatan',
    ];
}

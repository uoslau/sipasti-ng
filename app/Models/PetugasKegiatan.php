<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetugasKegiatan extends Model
{
    use HasFactory;

    // public $timestamps = false;

    protected $guarded = [];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}

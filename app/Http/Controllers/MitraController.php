<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    public function index()
    {
        $mitra = Mitra::orderBy('nama_mitra', 'asc')->paginate(20);

        return view('mitra.index', [
            'mitra' => $mitra,
        ]);
    }
}

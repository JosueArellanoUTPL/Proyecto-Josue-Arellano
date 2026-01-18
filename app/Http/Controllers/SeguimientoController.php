<?php

namespace App\Http\Controllers;

use App\Models\Meta;

class SeguimientoController extends Controller
{
    public function index()
    {
        $metas = Meta::with(['plan', 'indicadores.ultimoAvance'])->orderBy('id', 'desc')->get();
        return view('seguimiento.metas', compact('metas'));
    }

    public function show(Meta $meta)
    {
        $meta->load(['plan', 'indicadores.ultimoAvance', 'indicadores.avances']);
        return view('seguimiento.meta_show', compact('meta'));
    }
}

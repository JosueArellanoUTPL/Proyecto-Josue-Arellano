<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoAvance;
use App\Models\ProyectoAvanceEvidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProyectoAvanceController extends Controller
{
    public function create(Proyecto $proyecto)
    {
        $proyecto->load(['entidad', 'programa']);
        return view('seguimiento.proyecto_avance_create', compact('proyecto'));
    }

    public function store(Request $request, Proyecto $proyecto)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'porcentaje_avance' => ['required', 'numeric', 'min:0', 'max:100'],
            'comentario' => ['nullable', 'string', 'max:1000'],
            'evidencias' => ['nullable', 'array'],
            'evidencias.*' => ['file', 'max:5120'], // 5MB
        ]);

        $avance = ProyectoAvance::create([
            'proyecto_id' => $proyecto->id,
            'user_id' => Auth::id(),
            'fecha' => $data['fecha'],
            'porcentaje_avance' => $data['porcentaje_avance'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                if (!$file) continue;

                $path = $file->store('evidencias/proyectos', 'public');

                ProyectoAvanceEvidencia::create([
                    'proyecto_avance_id' => $avance->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('seguimiento.proyecto.show', $proyecto->id)
            ->with('success', 'Avance registrado correctamente.');
    }
}

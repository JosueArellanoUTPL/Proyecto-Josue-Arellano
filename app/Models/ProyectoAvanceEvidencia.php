<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoAvanceEvidencia extends Model
{
    protected $fillable = [
        'proyecto_avance_id',
        'path',
        'original_name',
        'mime_type',
        'size'
    ];

    public function avance()
    {
        return $this->belongsTo(ProyectoAvance::class);
    }

    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}


<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    // Cargar plantilla de autenticacion.
    public function render(): View
    {
        return view('layouts.guest');
    }
}

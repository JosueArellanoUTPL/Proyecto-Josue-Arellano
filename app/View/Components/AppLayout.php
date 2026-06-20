<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    // Cargar plantilla principal.
    public function render(): View
    {
        return view('layouts.app');
    }
}

<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Represents the GuestLayout application component.
 */
class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    /**
     * Handles render.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}

<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Represents the AppLayout application component.
 */
class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    /**
     * Handles render.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}

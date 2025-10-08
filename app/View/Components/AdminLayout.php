<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

<<<<<<< HEAD
class adminLayout extends Component
=======
class AdminLayout extends Component
>>>>>>> 249b43ae89a259d1552be25f196090e08bacb3b8
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.admin');
    }
}

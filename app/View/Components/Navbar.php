<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class Navbar extends Component
{
    /**
     * Create a new component instance.
     */
    public $title;

    public $background;

    public $guard;

    public function __construct(string $title, string $background, $guard = 'admin')
    {
        $this->title = $title;
        $this->background = $background;
        $this->guard = $guard;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('components.navbar');
    }
}

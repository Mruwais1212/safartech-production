<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public $background;

    public $privileges;

    public $guard;

    public function __construct(string $background, $privileges, $guard = 'admin')
    {
        $this->background = $background;
        $this->privileges = $privileges;
        $this->guard = $guard;
    }

    public function render()
    {
        return view('components.sidebar');
    }
}

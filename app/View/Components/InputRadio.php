<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputRadio extends Component
{
    public $placeholder;

    public $class;

    public function __construct($placeholder, $class = null)
    {
        $this->placeholder = $placeholder;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.input-radio');
    }
}

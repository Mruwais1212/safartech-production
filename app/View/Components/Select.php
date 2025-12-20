<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    public $key;

    public $placeholder;

    public $class;

    public $multiple;

    public function __construct($key, $placeholder, $class = null, $multiple = false)
    {
        $this->key = $key;
        $this->placeholder = $placeholder;
        $this->class = $class;
        $this->multiple = $multiple;
    }

    public function render()
    {
        return view('components.select');
    }
}

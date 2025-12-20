<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class Input extends Component
{
    public $object;

    public $key;

    public $placeholder;

    public $type;

    public $class;

    public function __construct($key, $type, $placeholder, $object = null, $class = null)
    {
        $this->object = $object;
        $this->key = $key;
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('components.input');
    }
}

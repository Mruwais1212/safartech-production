<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class Textarea extends Component
{
    public $object;

    public $key;

    public $placeholder;

    public $class;

    public function __construct($key, $placeholder, $object = null, $class = null)
    {
        $this->object = $object;
        $this->key = $key;
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
        return view('components.textarea');
    }
}

<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class Details extends Component
{
    public $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('components.details');
    }
}

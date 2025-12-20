<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SmallDetails extends Component
{
    public $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function render()
    {
        return view('components.small-details');
    }
}

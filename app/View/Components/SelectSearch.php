<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class SelectSearch extends Component
{
    public $key;

    public $placeholder;

    public $class;

    public $formClass;

    public function __construct($key, $placeholder, $class = null, $formClass = null)
    {
        $this->key = $key;
        $this->placeholder = $placeholder;
        $this->class = $class;
        $this->formClass = $formClass;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('components.select-search');
    }
}

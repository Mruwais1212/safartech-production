<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class InputSearch extends Component
{
    public $key;

    public $placeholder;

    public $type;

    public $class;

    public $formClass;

    public function __construct($key, $type, $placeholder, $class = null, $formClass = null)
    {
        $this->key = $key;
        $this->type = $type;
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
        return view('components.input-search');
    }
}

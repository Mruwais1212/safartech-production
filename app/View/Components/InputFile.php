<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputFile extends Component
{
    public $object;

    public $key;

    public $placeholder;

    public $prefix;

    public $options;

    public function __construct($key, $placeholder, $prefix, $object = null, $options = null)
    {
        $this->object = $object;
        $this->key = $key;
        $this->placeholder = $placeholder;
        $this->prefix = $prefix;
        $this->options = $options;
    }

    public function render()
    {
        return view('components.input-file');
    }
}

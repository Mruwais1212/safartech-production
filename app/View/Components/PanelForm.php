<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PanelForm extends Component
{
    public $title;

    public $object;

    public $url;

    public $formMethod;

    public function __construct($title, $url, $object = null, $formMethod = 'PUT')
    {
        $this->url = $url;
        $this->object = $object;
        $this->title = $title;
        $this->formMethod = $formMethod;
    }

    public function render()
    {
        return view('components.panel-form');
    }
}

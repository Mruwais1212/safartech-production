<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class PanelDatatable extends Component
{
    /**
     * Create a new component instance.
     */
    public $title;

    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('components.panel-datatable');
    }
}

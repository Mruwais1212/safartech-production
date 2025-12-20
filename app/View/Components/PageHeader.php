<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class PageHeader extends Component
{
    /**
     * Create a new component instance.
     */
    public $header;

    public $url;

    public function __construct($header, $url = null)
    {
        $this->header = $header;
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('components.page-header');
    }
}

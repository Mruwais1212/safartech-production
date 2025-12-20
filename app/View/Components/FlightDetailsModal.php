<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class FlightDetailsModal extends Component
{
    /**
     * Create a new component instance.
     */
    public $segments;
    public function __construct($segments=null)
    {
        $this->segments=$segments;
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */

    public function render()
    {
        return view('components.flight-details-modal');
    }
}

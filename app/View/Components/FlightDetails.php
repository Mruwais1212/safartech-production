<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class FlightDetails extends Component
{
    /**
     * Create a new component instance.
     */
    public $flightSegment;
    public $inboundFlightSegment;
    public function __construct($flightSegment=null,$inboundFlightSegment=null)
    {
        $this->flightSegment=$flightSegment;
        $this->inboundFlightSegment=$inboundFlightSegment;
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */

    public function render()
    {
        return view('components.flight-details');
    }
}

<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class ActivityDetails extends Component
{
    /**
     * Create a new component instance.
     */
    public $activity;

    public $prefix;

    public function __construct($activity, $prefix)
    {
        $this->activity = $activity;
        $this->prefix = $prefix;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('components.activity-details');
    }
}

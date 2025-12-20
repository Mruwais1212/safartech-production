<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatisticCard extends Component
{
    /**
     * Create a new component instance.
     */
    public $statistics;

    public $icons;

    public $links;

    public function __construct($statistics, $icons, $links = [])
    {
        $this->statistics = $statistics;
        $this->icons = $icons;
        $this->links = $links;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.statistic-card');
    }
}

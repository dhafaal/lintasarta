<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class statsCard extends Component
{
    public $title;
    public $count;
    public $subtitle;
    public $bgColor;
    public $icon;

    public function __construct($title, $count, $subtitle, $bgColor, $icon)
    {
        $this->title = $title;
        $this->count = $count;
        $this->subtitle = $subtitle;
        $this->bgColor = $bgColor;
        $this->icon = $icon;
    }


    public function render(): View|Closure|string
    {
        return view('components.admin.stats-card');
    }
}

<?php

namespace DD4You\Dpanel\View\Components\Card;

use Illuminate\View\Component;

class Style1 extends Component
{
    public $count;
    public function __construct($count = 0)
    {
        $this->count = $count;
    }

    public function render()
    {
        return view('components.card.style1');
    }
}

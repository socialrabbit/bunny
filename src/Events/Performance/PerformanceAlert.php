<?php

namespace Bunny\Events\Performance;

class PerformanceAlert
{
    public $metric;
    public $value;

    public function __construct($metric, $value)
    {
        $this->metric = $metric;
        $this->value = $value;
    }
}

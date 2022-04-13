<?php namespace Theory\Tests\Unit\Di\Fixtures;

class ValueParameter
{
    public $property1;

    public function __construct($param1)
    {
        $this->property1 = $param1;
    }
}
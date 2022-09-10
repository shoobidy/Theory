<?php namespace Theory\Tests\Unit\Di\Fixtures;

class ValueParameter
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}
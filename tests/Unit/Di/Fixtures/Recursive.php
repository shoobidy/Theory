<?php namespace Theory\Tests\Unit\Di\Fixtures;

class Recursive
{
    public $obj;

    public function __construct(ObjectParameter $obj)
    {
        $this->obj = $obj;
    }
}
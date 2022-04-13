<?php namespace Theory\Tests\Unit\Di\Fixtures;

class ObjectParameter
{
    public $obj;
    
    public function __construct(NullObject $obj)
    {
        $this->obj = $obj;
    }
}
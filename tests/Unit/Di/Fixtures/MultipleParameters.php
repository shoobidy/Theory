<?php namespace Theory\Tests\Unit\Di\Fixtures;

class MultipleParameters
{
    public $obj;
    public $value;
    
    public function __construct(NullObject $obj, $value)
    {
        $this->obj = $obj;
        $this->value = $value;
    }
}
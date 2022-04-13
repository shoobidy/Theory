<?php namespace Theory\Tests\Unit\Di\Fixtures;

class DefaultParameter
{
    public $param;
    
    public function __construct(string $param = 'test')
    {
        $this->param = $param;
    }
}
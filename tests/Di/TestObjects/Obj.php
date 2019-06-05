<?php namespace Theory\Tests\Di\TestObjects;

class Obj
{
    private $value;

    public function set($value)
    {
        $this->value = $value;
    }

    public function get()
    {
        return $this->value;
    }
}

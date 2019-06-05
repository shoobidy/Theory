<?php namespace Theory\Tests\Di\TestObjects;

class AutowireRecursive
{
    public function __construct(Autowire $a, Autowire $b){}
}

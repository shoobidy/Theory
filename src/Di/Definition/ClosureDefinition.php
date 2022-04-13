<?php namespace Theory\Di\Definition;

class ClosureDefinition extends AbstractDefinition
{
    public function resolve($container)
    {
        return ($this->value)($container);
    }
}
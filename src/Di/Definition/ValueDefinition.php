<?php namespace Theory\Di\Definition;

class ValueDefinition extends AbstractDefinition
{
    public function resolve($container)
    {
        return $this->value;
    }
}
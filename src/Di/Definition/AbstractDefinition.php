<?php namespace Theory\Di\Definition;

abstract class AbstractDefinition
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    abstract public function resolve($container);

    public function toArray()
    {
        return ['value' => $this->value];
    }
}
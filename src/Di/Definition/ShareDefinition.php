<?php namespace Theory\Di\Definition;

class ShareDefinition extends AbstractDefinition
{
    public function resolve($container)
    {
        return $container->getSharedObject($this->value);
    }
}
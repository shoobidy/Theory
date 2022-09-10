<?php namespace Theory\Di\Exception;

use Theory\TheoryException;
use ReflectionParameter;

class UndefinedParameter extends TheoryException
{
    private $parameter;

    public function __construct(ReflectionParameter $parameter)
    {
        $this->parameter = $parameter;

        parent::__construct(
            'Undefined parameter: ' . $parameter->getDeclaringClass()->getName() . '($' . $parameter->getName() . ')'
        );
    }

    public function getParameterName()
    {
        return $this->parameter->getName();
    }
}
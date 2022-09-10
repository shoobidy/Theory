<?php namespace Theory\Di\Exception;

use Theory\TheoryException;

class NotClosure extends TheoryException
{
    public function __construct($value)
    {
        $type = gettype($value);

        parent::__construct("Expecting a closure. $type given. '$value'");
    }
}
<?php namespace Theory\Di\Exception;

use Theory\TheoryException;

class ClassNotShared extends TheoryException
{
    public function __construct(string $id)
    {
        parent::__construct("$id was not found in the shared object list");
    }
}
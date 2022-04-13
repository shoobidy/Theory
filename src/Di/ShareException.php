<?php namespace Theory\Di;

use Theory\TheoryException;

class ShareException extends TheoryException
{
    public function __construct(string $id)
    {
        parent::__construct("$id was not found in the shared object list");
    }
}
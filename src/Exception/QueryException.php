<?php

namespace GraphQLClient\Exception;

class QueryException extends \RuntimeException
{
    protected $errorData;

    public function __construct($errorData)
    {
        $this->errorData = $errorData;
        parent::__construct($this->errorData['message']);
    }

    public function getLocations()
    {
        return $this->errorData['locations'] ?? '';
    }

    public function getPath()
    {
        return $this->errorData['path'] ?? '';
    }
}

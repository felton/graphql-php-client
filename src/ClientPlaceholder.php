<?php
namespace GraphQLClient;

class ClientPlaceholder
{
    public $didRun = false;

    public function __construct()
    {
        echo 'Hello world';

        $this->didRun = true;
    }
}

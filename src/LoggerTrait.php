<?php

namespace GraphQLClient;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

trait Logging
{
    protected $logger;

    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger;
    }

    // public function log()
    // {
    //     $this->logger->log();
    // }
}

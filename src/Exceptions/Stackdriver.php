<?php

namespace Ferranfg\Base\Exceptions;

use Monolog\Logger;
use Monolog\Handler\PsrHandler;
use Google\Cloud\Logging\LoggingClient;

class Stackdriver
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $logName = isset($config['logName']) ? $config['logName'] : 'app';

        $handler = new PsrHandler(
            LoggingClient::psrBatchLogger($logName)
        );

        $logger = new Logger($logName, [$handler]);

        return $logger;
    }
}
<?php

namespace Ferranfg\Base\Exceptions;

use Google\Cloud\Logging\LoggingClient;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;

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

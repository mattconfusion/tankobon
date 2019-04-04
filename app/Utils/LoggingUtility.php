<?php

namespace Utils;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggingUtility {
    
    private static $instance = null;
    private $logger;

    public static function getLogger($destinationFolder)
    {
        if (!self::$instance) {
            self::$instance = new LoggingUtility($destinationFolder);
        }
    }

    public function __construct($destinationFolder)
    {
        $this->logger = new Logger('tankobon');
        $this->logger->pushHandler(
            new StreamHandler(
                $destinationFolder . DIRECTORY_SEPARATOR .'/tankobon' . time() . '.log', 
                Logger::DEBUG
            )
        );
    }

    public function logInfo($message)
    {
        $this->log($message, Logger::INFO);
    }

    private function log($message, $level)
    {
        $this->logger->log($level, $message);
    }
}
<?php

declare(strict_types=1);

namespace Serato\AppEvents\Test;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /** @var string */
    private $logFilePath = '';

    protected function setUp()
    {
        $this->logFilePath = sys_get_temp_dir() . '/php-unit-log.log';
        // $this->logger = new Logger("PHP-Unit-Logger");
        // $this->logger->pushHandler(new StreamHandler($this->logFilePath, Logger::DEBUG));
        // // Format log entries as JSON. Makes them easier to parse in our tests :-)
        // foreach ($this->logger->getHandlers() as $handler) {
        //     $handler->setFormatter(new JsonFormatter());
        // }
    }

    protected function tearDown()
    {
        if (file_exists($this->getLogFilePath())) {
            unlink($this->getLogFilePath());
        }
    }

    protected function getLogFilePath(): string
    {
        return $this->logFilePath;
    }

    protected function getLogFileContents(): string
    {
        $log = file_get_contents($this->getLogFilePath());
        return $log === false ? '' : $log;
    }
}

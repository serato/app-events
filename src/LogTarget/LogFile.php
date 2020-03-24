<?php
declare(strict_types=1);

namespace Serato\AppEvents\LogTarget;

use Serato\AppEvents\Event\LoggableEventInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;

class LogFile extends AbstractLogTarget
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * Constructs the object
     *
     * @param string $appName
     * @param string $env
     */
    public function __construct(string $appName, string $env, string $logFilePath)
    {
        parent::__construct($appName, $env);

        $stream = new StreamHandler($logFilePath, Logger::INFO);
        $stream->setFormatter(new JsonFormatter);
        $this->logger = new Logger('serato_eventlog');
        $this->logger->pushHandler($stream);
    }

    /**
     * {@inheritdoc}
     */
    protected function log(LoggableEventInterface $event): void
    {
        $this->logger->info($event->getName(), $event->get());
    }

    /**
     * Returns the PSR LoggerInterface instance
     *
     * @return Logger
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}

<?php
declare(strict_types=1);

namespace Serato\AppEvents\EventTarget;

use Serato\AppEvents\Event\SendableEventInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;

class LogFile extends AbstractEventTarget
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
        $this->logger = new Logger('serato_app_events');
        $this->logger->pushHandler($stream);
    }

    /**
     * {@inheritdoc}
     */
    protected function send(SendableEventInterface $event): void
    {
        $this->logger->info($event->getEventCategory() . '.' . $event->getEventAction(), $event->get());
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

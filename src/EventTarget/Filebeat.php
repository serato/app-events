<?php
declare(strict_types=1);

namespace Serato\AppEvents\EventTarget;

use Serato\AppEvents\Event\SendableEventInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;

class Filebeat extends AbstractEventTarget
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * Constructs the object
     *
     * @param string $appName
     */
    public function __construct(string $appName, string $logFilePath)
    {
        parent::__construct($appName);

        $stream = new StreamHandler($logFilePath, Logger::INFO);
        $stream->setFormatter(new JsonFormatter);
        $this->logger = new Logger($appName . ' Filebeat Logger');
        $this->logger->pushHandler($stream);
    }

    /**
     * {@inheritdoc}
     */
    protected function send(SendableEventInterface $event): void
    {
        $this->logger->info($event->getEventActionCategory() . '.' . $event->getEventActionName(), $event->get());
    }

    /**
     * Returns the PSR LoggerInterface instance
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}

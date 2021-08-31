<?php

declare(strict_types=1);

namespace Serato\AppEvents\Test\EventTarget;

use Serato\AppEvents\Test\AbstractTestCase;
use Serato\AppEvents\EventTarget\Filebeat;
use Serato\AppEvents\Event\SeraTo\Redirect;

class FilebeatTest extends AbstractTestCase
{
    public function testTest(): void
    {
        $fb = new Filebeat('My App', $this->getLogFilePath());
        $fb->sendEvent($this->getRedirectEvent());

        $logItems = explode("\n", trim($this->getLogFileContents()));

        $this->assertEquals(1, count($logItems));
    }

    protected function getRedirectEvent(): Redirect
    {
        $event  = new Redirect();
        $event
            # AbstractTimeSeriesEvent
            ->setHttpReferrer('http://serato.com/dj')
            ->setClientIp('24.30.52.126')
            ->setUserAgent(
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 ' .
                '(KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36'
            )
            # Redirect
            ->setRedirectId('id-123')
            ->setRedirectName('Manage Subscription')
            ->setRedirectGroup('Serato DJ (app)')
            ->setRedirectShortUrl('sera.to/-b6ap')
            ->setRedirectDestinationUrl('https://account.serato.com/#/subscriptions')
        ;
        return $event;
    }
}

<?php
declare(strict_types=1);

namespace Serato\AppEvents\Test\Event\SeraTo;

use Serato\AppEvents\Test\AbstractTestCase;
use Serato\AppEvents\Event\SeraTo\Redirect;

class RedirectTest extends AbstractTestCase
{
    public function testSmokeTest(): void
    {
        $event  = new Redirect;
        $event
            # AbstractEventDataContainer
            ->setAppName('My App')
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
        $this->assertTrue(is_array($event->get()));
        $this->assertEquals([$event->getEventActionCategory(), $event->getEventActionName()], $event->getEventAction());
    }
}

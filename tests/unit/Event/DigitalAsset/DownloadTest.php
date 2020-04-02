<?php
declare(strict_types=1);

namespace Serato\AppEvents\Test\Event\DigitalAsset;

use Serato\AppEvents\Test\AbstractTestCase;
use Serato\AppEvents\Event\DigitalAsset\Download;
use DateTime;

class DownloadTest extends AbstractTestCase
{
    public function testSmokeTest(): void
    {
        $id = 'dl-1234';
        $eventStart = new DateTime('2020-01-01T05:30:30+00:00');
        $outcome = Download::FAILURE;

        $versionRelease = '1.2.3';
        $versionBuild = '456';

        $event  = new Download;
        $event
            # AbstractTimeSeriesEvent
            ->setUserId('user-123')
            ->setClientIp('24.30.52.126')
            ->setUserAgent(
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 ' .
                '(KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36'
            )
            ->setEventId($id)
            ->setEventStart($eventStart)
            # Download
            ->setFileId('file-789')
            ->setFileKey('/path/installer.dmg')
            ->setFileExtension('dmg')
            ->setFileSize(1234567)
            ->setEventOutcome($outcome)
            ->buildResourceInfo(
                'dj',
                'win-installer-no-corepack',
                Download::RELEASE,
                $versionRelease . '.' . $versionBuild,
                'Name of content pack'
            )
        ;

        $this->assertTrue(is_array($event->get()));
        $this->assertEquals($event->getEventStart(), $eventStart);
        $this->assertEquals($event->getEventId(), $id);
        $this->assertEquals($event->getEventOutcome(), $outcome);
        $this->assertEquals([$event->getEventActionCategory(), $event->getEventActionName()], $event->getEventAction());
        $this->assertEquals($versionRelease, $event->getVersionNumberRelease());
        $this->assertEquals($versionRelease . '.' . $versionBuild, $event->getVersionNumberBuild());
        $this->assertEquals(102003000456, $event->getVersionNumberInt());
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvalidBuildResourceInfoRelaseType(): void
    {
        $event = new Download;
        $event->buildResourceInfo(
            'dj',
            'win-installer-no-corepack',
            'NO-SUCH-RELASE-TYPE',
            '1.2.3.456',
            'Name of content pack'
        );
    }
}

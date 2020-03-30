<?php
declare(strict_types=1);

namespace Serato\AppEvents\Test\Event\SoftwareLicense;

use Serato\AppEvents\Test\AbstractTestCase;
use Serato\AppEvents\Event\SoftwareLicense\Authorization;
use DateTime;

class AuthorizationTest extends AbstractTestCase
{
    public function testSmokeTest(): void
    {
        $event  = new Authorization;
        $event
            # AbstractTimeSeriesEvent
            ->setUserId('user-123')
            ->setClientIp('24.30.52.126')
            ### ->setEventId()
            ->setEventEnd(new DateTime)
            ->setEventOutcome(Authorization::SUCCESS)
            # Authorization
            ->setAuthorizationId('id-789')
            ->setAuthorizationValidTo(new DateTime)
            ->setAuthorizationCommittedAt(new DateTime)
            ->setAuthorizationResultCode(0)
            ->setAuthorizationAction(Authorization::AUTHORIZE)
            ->setAuthorizationHostMachineId('P57TL8GGQI69~GBFUL623C0UIG~PG796169S564N489')
            ->setAuthorizationHostName('My Amazing Computer')
            ->setAuthorizationHostOs('win')
            ->setAuthorizationHostLocale('NZ')
            ->setAuthorizationHostApplicationName('dj')
            ->setAuthorizationHostApplicationVersion('1.2.3.456')
            ->setLicenseId('SDJ-4617674-0032-4310-6504')
            ->setLicenseValidTo(new DateTime)
            ->setLicenseTypeId('32')
            ->setLicenseTypeName('Flip Expansion Pack')
            ->setLicenseTypeOptions(Authorization::PERMANENT)
            ->setLicenseTypeRlmSchemaName('flip_pack')
            ->setLicenseTypeRlmSchemaVersion('1.0')
            ->setLicenseUserId('user-456')
            ->setLicenseProductId('SDJ-4617674-0085-1834-4607')
            ->setLicenseProductCreatedAt(new DateTime)
            ->setLicenseProductTypeId('85')
            ->setLicenseProductTypeName('Serato Flip Expansion Pack [download]')
        ;
        $this->assertTrue(is_array($event->get()));
        $this->assertEquals([$event->getEventActionCategory(), $event->getEventActionName()], $event->getEventAction());
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvalidAuthorizationAction(): void
    {
        $event = new Authorization;
        $event->setAuthorizationAction('THIS-IS-NOT-LEGIT');
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvalidLicenseTypeOptions(): void
    {
        $event = new Authorization;
        $event->setLicenseTypeOptions('THIS-IS-NOT-LEGIT');
    }

    /**
     * @expectedException \Serato\AppEvents\Exception\InvalidDataValueException
     */
    public function testInvalidEventOutcome(): void
    {
        $event = new Authorization;
        $event->setEventOutcome('THIS-IS-NOT-LEGIT');
    }

    /**
     * Provide a value to Authorization::setAuthorizationHostMachineId that is not
     * a legitimate, parse-able host ID.
     *
     * This should not error, but there will be no host ID data added to the object.
     *
     * @return void
     */
    public function testInvalidAuthorizationHostMachineId(): void
    {
        $event = new Authorization;
        $event->setAuthorizationHostMachineId('NOT-A-HOST-ID');
        # Should only have default event data in the output
        $this->assertEquals(count($event->get()), 1);
        $this->assertTrue(isset($event->get()['event']));
    }
}

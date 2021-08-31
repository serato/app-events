<?php

declare(strict_types=1);

namespace Serato\AppEvents\Event\SoftwareLicense;

use DateTime;
use Serato\AppEvents\HostMachineUid;
use Serato\AppEvents\Event\AbstractTimeSeriesEvent;
use Serato\AppEvents\Exception\InvalidHostMachineUidException;

/**
 * ** Authorization **
 *
 * Captures data attributes related to a license authorisation event and allows the
 * data to to be send to a logging target.
 *
 * Sets the following fields:
 *
 * `<APP EVENT ROOT ATTR>.authorization.id`
 * `<APP EVENT ROOT ATTR>.authorization.valid_to`
 * `<APP EVENT ROOT ATTR>.authorization.comitted_at`
 * `<APP EVENT ROOT ATTR>.authorization.result_code`
 * `<APP EVENT ROOT ATTR>.authorization.action`
 * `<APP EVENT ROOT ATTR>.authorization.host.machine.id.raw`
 * `<APP EVENT ROOT ATTR>.authorization.host.machine.id.canonical`
 * `<APP EVENT ROOT ATTR>.authorization.host.machine.id.system_id`
 * `<APP EVENT ROOT ATTR>.authorization.host.machine.name`
 * `<APP EVENT ROOT ATTR>.authorization.host.os.family`
 * `<APP EVENT ROOT ATTR>.authorization.host.locale`
 * `<APP EVENT ROOT ATTR>.authorization.host.application.name`
 * `<APP EVENT ROOT ATTR>.authorization.host.application.version.release`
 * `<APP EVENT ROOT ATTR>.authorization.host.application.version.build`
 * `<APP EVENT ROOT ATTR>.authorization.host.application.version.build_int`
 * `<APP EVENT ROOT ATTR>.license.id`
 * `<APP EVENT ROOT ATTR>.license.valid_to`
 * `<APP EVENT ROOT ATTR>.license.license_type.id`
 * `<APP EVENT ROOT ATTR>.license.license_type.name`
 * `<APP EVENT ROOT ATTR>.license.license_type.term`
 * `<APP EVENT ROOT ATTR>.license.license_type.rlm_schema.name`
 * `<APP EVENT ROOT ATTR>.license.license_type.rlm_schema.version`
 * `<APP EVENT ROOT ATTR>.license.user.id`
 * `<APP EVENT ROOT ATTR>.license.product.id`
 * `<APP EVENT ROOT ATTR>.license.product.created_at`
 * `<APP EVENT ROOT ATTR>.license.product.product_type.id`
 * `<APP EVENT ROOT ATTR>.license.product.product_type.name`
 */
class Authorization extends AbstractTimeSeriesEvent
{
    public const AUTHORIZE = 'Authorize';
    public const DEAUTHORIZE = 'De-authorize';
    public const PERMANENT = 'P';
    public const TIMELIMITED = 'L';
    public const SUBSCRIPTION = 'S';
    public const TRIAL = 'T';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getEventActionCategory(): string
    {
        return 'software_license';
    }

    /**
     * {@inheritdoc}
     */
    public function getEventActionName(): string
    {
        return 'authorization';
    }

    /**
     * Sets the authorization ID
     *
     * Sets the following field(s):
     *
     * `event.id`
     * `<APP EVENT ROOT ATTR>.authorization.id`
     *
     * @param string $id
     * @return self
     */
    public function setAuthorizationId(string $id): self
    {
        return $this
            ->setEventId($id)
            ->setAppEventRootAttributeData('authorization.id', $id);
    }

    /**
     * Sets the authorization `valid to` date
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.authorization.valid_to`
     *
     * @param DateTime $dt
     * @return self
     */
    public function setAuthorizationValidTo(DateTime $dt): self
    {
        return $this->setAppEventRootAttributeData('authorization.valid_to', $dt->format(DateTime::ATOM));
    }

    /**
     * Sets the authorization `comitted at` date
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.authorization.comitted_at`
     *
     * @param DateTime $dt
     * @return self
     */
    public function setAuthorizationCommittedAt(DateTime $dt): self
    {
        return $this->setAppEventRootAttributeData('authorization.comitted_at', $dt->format(DateTime::ATOM));
    }

    /**
     * Sets the authorization result code
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.authorization.result_code`
     *
     * @param integer $code
     * @return self
     */
    public function setAuthorizationResultCode(int $code): self
    {
        return $this->setAppEventRootAttributeData('authorization.result_code', $code);
    }

    /**
     * Sets the authorisation action.
     *
     * Transposes the value of `Authorize` to `activate`, and `De-authorize` to `deactivate`.
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.authorization.action`
     *
     * @param string $action
     * @return self
     */
    public function setAuthorizationAction(string $action): self
    {
        $this->validateDataValue($action, [self::AUTHORIZE, self::DEAUTHORIZE], __METHOD__);
        $v = $action;
        if ($action == 'Authorize') {
            $v = 'activate';
        };
        if ($action == 'De-authorize') {
            $v = 'deactivate';
        };
        return $this->setAppEventRootAttributeData('authorization.action', $v);
    }

    /**
     * Sets the authorizaton host machine ID.
     *
     * Sets the raw host ID then parses the raw host ID and sets the canonical form of the host ID
     * as well as the host system ID.
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.authorization.host.machine.id.raw`
     * `<APP EVENT ROOT ATTR>.authorization.host.machine.id.canonical`
     * `<APP EVENT ROOT ATTR>.authorization.host.machine.id.system_id`
     *
     * @param string $hostId
     * @return self
     */
    public function setAuthorizationHostMachineId(string $hostId): self
    {
        try {
            $hostMachineUid = new HostMachineUid($hostId);
            return $this
                ->setAppEventRootAttributeData(
                    'authorization.host.machine.id.raw',
                    (string)$hostMachineUid
                )
                ->setAppEventRootAttributeData(
                    'authorization.host.machine.id.canonical',
                    $hostMachineUid->getCanonicalHostId()
                )
                ->setAppEventRootAttributeData(
                    'authorization.host.machine.id.system_id',
                    $hostMachineUid->getSystemId()
                );
        } catch (InvalidHostMachineUidException $e) {
            // Ignore invalid values
        }
        return $this;
    }

    /**
     * Sets the authorization host machine name
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.authorization.host.machine.name`
     *
     * @param string $name
     * @return self
     */
    public function setAuthorizationHostName(string $name): self
    {
        return $this->setAppEventRootAttributeData('authorization.host.machine.name', $name);
    }

    /**
     * Sets the authorization host OS family name
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.authorization.host.os.family`
     *
     * @param string $os
     * @return self
     */
    public function setAuthorizationHostOs(string $os): self
    {
        return $this->setAppEventRootAttributeData(
            'authorization.host.os.family',
            $this->getNormalizedOsPlatform($os)
        );
    }

    /**
     * Sets the authorization host locale
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.authorization.host.locale`
     *
     * @param string $locale
     * @return self
     */
    public function setAuthorizationHostLocale(string $locale): self
    {
        return $this->setAppEventRootAttributeData('authorization.host.locale', $locale);
    }

    /**
     * Sets authorization host application name and version
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.authorization.host.application.name`
     *
     * @param string $name
     * @return self
     */
    public function setAuthorizationHostApplicationName(string $name): self
    {
        return $this->setAppEventRootAttributeData(
            'authorization.host.application.name',
            $this->productShortNameToProductFamily($name)
        );
    }

    /**
     * Sets authorization host application version
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.authorization.host.application.version.release`
     * `<APP EVENT ROOT ATTR>.authorization.host.application.version.build`
     * `<APP EVENT ROOT ATTR>.authorization.host.application.version.build_int`
     *
     * @param string $version
     * @return self
     */
    public function setAuthorizationHostApplicationVersion(string $version): self
    {
        return $this->setAppEventRootAttributeData(
            'authorization.host.application.version',
            $this->getBuildNumberData($version)
        );
    }

    /**
     * Sets the license ID
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.id`
     *
     * @param string $id
     * @return self
     */
    public function setLicenseId(string $id): self
    {
        return $this->setAppEventRootAttributeData('license.id', $id);
    }

    /**
     * Sets the license `valid to` date
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.valid_to`
     *
     * @param DateTime $dt
     * @return self
     */
    public function setLicenseValidTo(DateTime $dt): self
    {
        return $this->setAppEventRootAttributeData('license.valid_to', $dt->format(DateTime::ATOM));
    }

    /**
     * Sets the license type ID
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.license_type.id`
     *
     * @param string $id
     * @return self
     */
    public function setLicenseTypeId(string $id): self
    {
        return $this->setAppEventRootAttributeData('license.license_type.id', $id);
    }

    /**
     * Sets the license type name
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.license_type.name`
     *
     * @param string $name
     * @return self
     */
    public function setLicenseTypeName(string $name): self
    {
        return $this->setAppEventRootAttributeData('license.license_type.name', $name);
    }

    /**
     * Sets the license type options
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.license_type.term`
     *
     * @param string $options
     * @return self
     */
    public function setLicenseTypeOptions(string $options): self
    {
        $this->validateDataValue(
            $options,
            [self::PERMANENT, self::TIMELIMITED, self::SUBSCRIPTION, self::TRIAL],
            __METHOD__
        );

        $term = 'permanent';
        switch ($options) {
            case self::TIMELIMITED:
                $term = 'timelimited';
                break;
            case self::SUBSCRIPTION:
                $term = 'subscription';
                break;
            case self::TRIAL:
                $term = 'trial';
                break;
        }
        return $this->setAppEventRootAttributeData('license.license_type.term', $term);
    }

    /**
     * Sets the license type RLM schema name
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.license_type.rlm_schema.name`
     *
     * @param string $name
     * @return self
     */
    public function setLicenseTypeRlmSchemaName(string $name): self
    {
        return $this->setAppEventRootAttributeData('license.license_type.rlm_schema.name', $name);
    }

    /**
     * Sets the license type RLM schema version
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.license_type.rlm_schema.version`
     *
     * @param string $v
     * @return self
     */
    public function setLicenseTypeRlmSchemaVersion(string $v): self
    {
        return $this->setAppEventRootAttributeData('license.license_type.rlm_schema.version', $v);
    }

    /**
     * Sets the license user ID
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.user.id`
     *
     * @param string $id
     * @return self
     */
    public function setLicenseUserId(string $id): self
    {
        return $this->setAppEventRootAttributeData('license.user.id', $id);
    }

    /**
     * Sets the license product ID
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.product.id`
     *
     * @param string $id
     * @return self
     */
    public function setLicenseProductId(string $id): self
    {
        return $this->setAppEventRootAttributeData('license.product.id', $id);
    }

    /**
     * Sets the license product creation date
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.product.created_at`
     *
     * @param DateTime $dt
     * @return self
     */
    public function setLicenseProductCreatedAt(DateTime $dt): self
    {
        return $this->setAppEventRootAttributeData(
            'license.product.created_at',
            $dt->format(DateTime::ATOM)
        );
    }

    /**
     * Sets the license product type ID
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.product.product_type.id`
     *
     * @param string $id
     * @return self
     */
    public function setLicenseProductTypeId(string $id): self
    {
        return $this->setAppEventRootAttributeData('license.product.product_type.id', $id);
    }

    /**
     * Sets the license product type name
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.license.product.product_type.name`
     *
     * @param string $name
     * @return self
     */
    public function setLicenseProductTypeName(string $name): self
    {
        return $this->setAppEventRootAttributeData('license.product.product_type.name', $name);
    }
}

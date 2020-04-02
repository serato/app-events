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
 * `<ROOT ATTR>.authorization.id`
 * `<ROOT ATTR>.authorization.valid_to`
 * `<ROOT ATTR>.authorization.comitted_at`
 * `<ROOT ATTR>.authorization.result_code`
 * `<ROOT ATTR>.authorization.action`
 * `<ROOT ATTR>.authorization.host.machine.id.raw`
 * `<ROOT ATTR>.authorization.host.machine.id.canonical`
 * `<ROOT ATTR>.authorization.host.machine.id.system_id`
 * `<ROOT ATTR>.authorization.host.machine.name`
 * `<ROOT ATTR>.authorization.host.os.family`
 * `<ROOT ATTR>.authorization.host.locale`
 * `<ROOT ATTR>.authorization.host.application.name`
 * `<ROOT ATTR>.authorization.host.application.version.release`
 * `<ROOT ATTR>.authorization.host.application.version.build`
 * `<ROOT ATTR>.authorization.host.application.version.build_int`
 * `<ROOT ATTR>.license.id`
 * `<ROOT ATTR>.license.valid_to`
 * `<ROOT ATTR>.license.license_type.id`
 * `<ROOT ATTR>.license.license_type.name`
 * `<ROOT ATTR>.license.license_type.term`
 * `<ROOT ATTR>.license.license_type.rlm_schema.name`
 * `<ROOT ATTR>.license.license_type.rlm_schema.version`
 * `<ROOT ATTR>.license.user.id`
 * `<ROOT ATTR>.license.product.id`
 * `<ROOT ATTR>.license.product.created_at`
 * `<ROOT ATTR>.license.product.product_type.id`
 * `<ROOT ATTR>.license.product.product_type.name`
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
     * `<ROOT ATTR>.authorization.id`
     *
     * @param string $id
     * @return self
     */
    public function setAuthorizationId(string $id): self
    {
        return $this
            ->setEventId($id)
            ->setEventRootAttributeData('authorization.id', $id);
    }

    /**
     * Sets the authorization `valid to` date
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.valid_to`
     *
     * @param DateTime $dt
     * @return self
     */
    public function setAuthorizationValidTo(DateTime $dt): self
    {
        return $this->setEventRootAttributeData('authorization.valid_to', $dt->format(DateTime::ATOM));
    }

    /**
     * Sets the authorization `comitted at` date
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.comitted_at`
     *
     * @param DateTime $dt
     * @return self
     */
    public function setAuthorizationCommittedAt(DateTime $dt): self
    {
        return $this->setEventRootAttributeData('authorization.comitted_at', $dt->format(DateTime::ATOM));
    }

    /**
     * Sets the authorization result code
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.result_code`
     *
     * @param integer $code
     * @return self
     */
    public function setAuthorizationResultCode(int $code): self
    {
        return $this->setEventRootAttributeData('authorization.result_code', $code);
    }

    /**
     * Sets the authorisation action.
     *
     * Transposes the value of `Authorize` to `activate`, and `De-authorize` to `deactivate`.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.action`
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
        return $this->setEventRootAttributeData('authorization.action', $v);
    }

    /**
     * Sets the authorizaton host machine ID.
     *
     * Sets the raw host ID then parses the raw host ID and sets the canonical form of the host ID
     * as well as the host system ID.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.host.machine.id.raw`
     * `<ROOT ATTR>.authorization.host.machine.id.canonical`
     * `<ROOT ATTR>.authorization.host.machine.id.system_id`
     *
     * @param string $hostId
     * @return self
     */
    public function setAuthorizationHostMachineId(string $hostId): self
    {
        try {
            $hostMachineUid = new HostMachineUid($hostId);
            return $this
                ->setEventRootAttributeData(
                    'authorization.host.machine.id.raw',
                    (string)$hostMachineUid
                )
                ->setEventRootAttributeData(
                    'authorization.host.machine.id.canonical',
                    $hostMachineUid->getCanonicalHostId()
                )
                ->setEventRootAttributeData(
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
     * `<ROOT ATTR>.authorization.host.machine.name`
     *
     * @param string $name
     * @return self
     */
    public function setAuthorizationHostName(string $name): self
    {
        return $this->setEventRootAttributeData('authorization.host.machine.name', $name);
    }

    /**
     * Sets the authorization host OS family name
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.host.os.family`
     *
     * @param string $os
     * @return self
     */
    public function setAuthorizationHostOs(string $os): self
    {
        return $this->setEventRootAttributeData(
            'authorization.host.os.family',
            $this->getNormalizedOsPlatform($os)
        );
    }

    /**
     * Sets the authorization host locale
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.host.locale`
     *
     * @param string $locale
     * @return self
     */
    public function setAuthorizationHostLocale(string $locale): self
    {
        return $this->setEventRootAttributeData('authorization.host.locale', $locale);
    }

    /**
     * Sets authorization host application name and version
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.host.application.name`
     *
     * @param string $name
     * @return self
     */
    public function setAuthorizationHostApplicationName(string $name): self
    {
        return $this->setEventRootAttributeData(
            'authorization.host.application.name',
            $this->productShortNameToProductFamily($name)
        );
    }

    /**
     * Sets authorization host application version
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.host.application.version.release`
     * `<ROOT ATTR>.authorization.host.application.version.build`
     * `<ROOT ATTR>.authorization.host.application.version.build_int`
     *
     * @param string $version
     * @return self
     */
    public function setAuthorizationHostApplicationVersion(string $version): self
    {
        return $this->setData(
            'authorization.host.application.version',
            $this->getBuildNumberData($version)
        );
    }

    /**
     * Sets the license ID
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.id`
     *
     * @param string $id
     * @return self
     */
    public function setLicenseId(string $id): self
    {
        return $this->setEventRootAttributeData('license.id', $id);
    }

    /**
     * Sets the license `valid to` date
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.valid_to`
     *
     * @param DateTime $dt
     * @return self
     */
    public function setLicenseValidTo(DateTime $dt): self
    {
        return $this->setEventRootAttributeData('license.valid_to', $dt->format(DateTime::ATOM));
    }

    /**
     * Sets the license type ID
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.license_type.id`
     *
     * @param string $id
     * @return self
     */
    public function setLicenseTypeId(string $id): self
    {
        return $this->setEventRootAttributeData('license.license_type.id', $id);
    }

    /**
     * Sets the license type name
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.license_type.name`
     *
     * @param string $name
     * @return self
     */
    public function setLicenseTypeName(string $name): self
    {
        return $this->setEventRootAttributeData('license.license_type.name', $name);
    }

    /**
     * Sets the license type options
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.license_type.term`
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
        return $this->setEventRootAttributeData('license.license_type.term', $term);
    }

    /**
     * Sets the license type RLM schema name
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.license_type.rlm_schema.name`
     *
     * @param string $name
     * @return self
     */
    public function setLicenseTypeRlmSchemaName(string $name): self
    {
        return $this->setEventRootAttributeData('license.license_type.rlm_schema.name', $name);
    }

    /**
     * Sets the license type RLM schema version
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.license_type.rlm_schema.version`
     *
     * @param string $v
     * @return self
     */
    public function setLicenseTypeRlmSchemaVersion(string $v): self
    {
        return $this->setEventRootAttributeData('license.license_type.rlm_schema.version', $v);
    }

    /**
     * Sets the license user ID
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.user.id`
     *
     * @param string $id
     * @return self
     */
    public function setLicenseUserId(string $id): self
    {
        return $this->setEventRootAttributeData('license.user.id', $id);
    }

    /**
     * Sets the license product ID
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.product.id`
     *
     * @param string $id
     * @return self
     */
    public function setLicenseProductId(string $id): self
    {
        return $this->setEventRootAttributeData('license.product.id', $id);
    }

    /**
     * Sets the license product creation date
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.product.created_at`
     *
     * @param DateTime $dt
     * @return self
     */
    public function setLicenseProductCreatedAt(DateTime $dt): self
    {
        return $this->setData(
            'license.product.created_at',
            $dt->format(DateTime::ATOM)
        );
    }

    /**
     * Sets the license product type ID
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.product.product_type.id`
     *
     * @param string $id
     * @return self
     */
    public function setLicenseProductTypeId(string $id): self
    {
        return $this->setEventRootAttributeData('license.product.product_type.id', $id);
    }

    /**
     * Sets the license product type name
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.product.product_type.name`
     *
     * @param string $name
     * @return self
     */
    public function setLicenseProductTypeName(string $name): self
    {
        return $this->setEventRootAttributeData('license.product.product_type.name', $name);
    }
}

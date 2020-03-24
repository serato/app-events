<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event;

use DateTime;
use Serato\AppEvents\HostMachineUid;
use Serato\AppEvents\Exception\InvalidHostMachineUidException;

/**
 * ** LicenseAuthorization **
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
class LicenseAuthorization extends AbstractTimeSeriesEvent
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
        $this->setEventCategory('license-authorization');
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'license_authorization';
    }

    /**
     * Sets the authorization ID
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.id`
     *
     * @param string $id
     * @return self
     */
    public function setAuthorizationId(string $id): self
    {
        return $this->setData(self::ROOT_ATTR . '.authorization.id', $id);
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
        return $this->setData(self::ROOT_ATTR . '.authorization.valid_to', $dt->format(DateTime::ATOM));
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
        return $this->setData(self::ROOT_ATTR . '.authorization.comitted_at', $dt->format(DateTime::ATOM));
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
        return $this->setData(self::ROOT_ATTR . '.authorization.result_code', $code);
    }

    /**
     * Sets the authorisation action.
     *
     * Transposes the value of `Authorize` to `activate`, and `De-authorize` to `deactivate`.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.action`
     * `event.action`
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
        return $this
            ->setEventAction($v)
            ->setData(self::ROOT_ATTR . '.authorization.action', $v);
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
                ->setData(self::ROOT_ATTR . '.authorization.host.machine.id.raw', (string)$hostMachineUid)
                ->setData(self::ROOT_ATTR . '.authorization.host.machine.id.canonical', $hostMachineUid->getCanonicalHostId())
                ->setData(self::ROOT_ATTR . '.authorization.host.machine.id.system_id', $hostMachineUid->getSystemId());
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
        return $this->setData(self::ROOT_ATTR . '.authorization.host.machine.name', $name);
    }

    /**
     * Sets the authorization host OS family name
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.host.os.family`
     *
     * @param string $os
     * @return string
     */
    public function setAuthorizationHostOs(string $os): self
    {
        return $this->setData(self::ROOT_ATTR . '.authorization.host.os.family', $this->getNormalizedOsPlatform($os));
    }

    /**
     * Sets the authorization host locale
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.host.locale`
     *
     * @param string $locale
     * @return string
     */
    public function setAuthorizationHostLocale(string $locale): self
    {
        return $this->setData(self::ROOT_ATTR . '.authorization.host.locale', $locale);
    }

    /**
     * Sets authorization host application name and version
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.authorization.host.application.name`
     * `<ROOT ATTR>.authorization.host.application.version.release`
     * `<ROOT ATTR>.authorization.host.application.version.build`
     * `<ROOT ATTR>.authorization.host.application.version.build_int`
     *
     * @param string $appName
     * @param string $appVersion
     * @return self
     */
    public function setAuthorizationHostApplication(string $appName, string $appVersion): self
    {
        $buildNumData = $this->getBuildNumberData($appVersion);
        return $this
            ->setData(
                self::ROOT_ATTR . '.authorization.host.application.name',
                $this->productShortNameToProductFamily($appName)
            )
            ->setData(self::ROOT_ATTR . '.authorization.host.application.version.release', $buildNumData['release'])
            ->setData(self::ROOT_ATTR . '.authorization.host.application.version.build', $buildNumData['build'])
            ->setData(self::ROOT_ATTR . '.authorization.host.application.version.build_int', $buildNumData['build_int']);
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
        return $this->setData(self::ROOT_ATTR . '.license.id', $id);
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
        return $this->setData(self::ROOT_ATTR . '.license.valid_to', $dt->format(DateTime::ATOM));
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
        return $this->setData(self::ROOT_ATTR . '.license.license_type.id', $id);
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
        return $this->setData(self::ROOT_ATTR . '.license.license_type.name', $name);
    }

    /**
     * Sets the license type options
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.license.license_type.term`
     *
     * @param string $term
     * @return self
     */
    public function setLicenseTypeOptions(string $options): self
    {
        $this->validateDataValue(
            $gateway,
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
        return $this->setData(self::ROOT_ATTR . '.license.license_type.term', $term);
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
        return $this->setData(self::ROOT_ATTR . '.license.license_type.rlm_schema.name', $name);
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
        return $this->setData(self::ROOT_ATTR . '.license.license_type.rlm_schema.version', $v);
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
        return $this->setData(self::ROOT_ATTR . '.license.user.id', $id);
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
        return $this->setData(self::ROOT_ATTR . '.license.product.id', $id);
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
        return $this->setData(self::ROOT_ATTR . '.license.product.created_at', $dt->format(DateTime::ATOM));
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
        return $this->setData(self::ROOT_ATTR . '.license.product.product_type.id', $id);
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
        return $this->setData(self::ROOT_ATTR . '.license.product.product_type.name', $name);
    }
}
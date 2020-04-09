<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event\DigitalAsset;

use Serato\AppEvents\Event\AbstractTimeSeriesEvent;

/**
 * ** Download **
 *
 * Captures data attributes related to an asset download event and allows the
 * data to to be send to a logging target.
 *
 * Assets, in this instance, refers to software application installers
 * and digital content packs.
 *
 * Sets the following fields:
 *
 * `<APP EVENT ROOT ATTR>.id`
 * `<APP EVENT ROOT ATTR>.key`
 * `<APP EVENT ROOT ATTR>.file.extension`
 * `<APP EVENT ROOT ATTR>.file.size`
 * `<APP EVENT ROOT ATTR>.file.name`
 * `<APP EVENT ROOT ATTR>.file.type`
 * `<APP EVENT ROOT ATTR>.file.content_pack.host_applications`
 * `<APP EVENT ROOT ATTR>.file.application_installer.product_family`
 * `<APP EVENT ROOT ATTR>.file.application_installer.release_type`
 * `<APP EVENT ROOT ATTR>.file.application_installer.os.platform`
 */
class Download extends AbstractTimeSeriesEvent
{
    public const RELEASE = 'release';
    public const PUBLIC_BETA = 'publicbeta';
    public const PRIVATE_BETA = 'privatebeta';

    public function __construct()
    {
        parent::__construct();
        # The ECS `file` field defines a `type` field with various valid values.
        # https://www.elastic.co/guide/en/ecs/current/ecs-file.html
        # For our purposes here it's always `file`.
        $this->setAppEventRootAttributeData('file.type', 'file');
        # The event always starts in the `unknown` state
        $this->setEventOutcome(self::UNKNOWN);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventActionCategory(): string
    {
        return 'digital_asset';
    }

    /**
     * {@inheritdoc}
     */
    public function getEventActionName(): string
    {
        return 'download';
    }

    /**
     * Sets the internal file ID of the downloaded file.
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.id`
     *
     * @param string $id
     * @return self
     */
    public function setFileId(string $id): self
    {
        return $this->setAppEventRootAttributeData('id', $id);
    }

    /**
     * Sets the internal file key of the downloaded file.
     * This is the key of the S3 object that stores the source file data.
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.key`
     *
     * @param string $key
     * @return self
     */
    public function setFileKey(string $key): self
    {
        return $this->setAppEventRootAttributeData('key', $key);
    }

    /**
     * Sets the file extension of the downloaded file.
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.file.extension`
     *
     * @param string $ext
     * @return self
     */
    public function setFileExtension(string $ext): self
    {
        return $this->setAppEventRootAttributeData('file.extension', $ext);
    }

    /**
     * Sets the total size (in bytes) of the downloaded file.
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.file.size`
     *
     * @param integer $bytes
     * @return self
     */
    public function setFileSize(int $bytes): self
    {
        return $this->setAppEventRootAttributeData('file.size', $bytes);
    }

    /**
     * Builds structured resource attributes from raw data.
     *
     * Sets the following field(s):
     *
     * `<APP EVENT ROOT ATTR>.file.name`
     * `<APP EVENT ROOT ATTR>.file.type`
     * `<APP EVENT ROOT ATTR>.file.content_pack.host_applications`
     * `<APP EVENT ROOT ATTR>.file.application_installer.product_family`
     * `<APP EVENT ROOT ATTR>.file.application_installer.release_type`
     * `<APP EVENT ROOT ATTR>.file.application_installer.os.platform`
     *
     * @param string $product       Product stream for the resource. eg. `scratchlive`. `dj`, `content`
     * @param string $resourceType  Type of resource eg. `win-installer`, `mac-installer-no-corepack`, `content-pack`
     * @param string $releaseType   One of `release`, `publicbeta` or `privatebeta`
     * @param string $version       Version number string
     * @param string|null $name     Name of resource
     * @return self
     */
    public function buildResourceInfo(
        string $product,
        string $resourceType,
        string $releaseType,
        string $version,
        ?string $name
    ): self {
        $this->validateDataValue(
            $releaseType,
            [self::RELEASE, self::PUBLIC_BETA, self::PRIVATE_BETA],
            __METHOD__,
            'releaseType'
        );
        $this->setAppEventRootAttributeData('version', $this->getBuildNumberData($version));

        if ($resourceType === 'content-pack') {
            $this->setAppEventRootAttributeData('type', 'content_pack');
            $this->setAppEventRootAttributeData('name', $name);
            # Yeah yeah, hardcoded for now.
            $this->setAppEventRootAttributeData('content_pack.host_applications', ['Serato Studio']);
        } else {
            $type = 'application_installer';
            if ($resourceType === 'win-installer-no-corepack' || $resourceType === 'mac-installer-no-corepack') {
                $type = 'application_installer_no_content';
            }
            $this->setAppEventRootAttributeData('type', $type);
            $productFamily = $this->productShortNameToProductFamily($product);
            $this->setAppEventRootAttributeData(
                'name',
                $productFamily . ' ' . $this->getVersionNumberRelease()
            );
            $this->setAppEventRootAttributeData('application_installer.product_family', $productFamily);
            $this->setAppEventRootAttributeData(
                'application_installer.os.platform',
                $this->getNormalizedOsPlatform($resourceType)
            );
            $this->setAppEventRootAttributeData('application_installer.release_type', $releaseType);
        }
        return $this;
    }

    /**
     * Returns the `build` form of a version number.
     *
     * The `build` form is the full version number string including
     * the build number. eg `1.2.3.456`.
     *
     * @return string|null
     */
    public function getVersionNumberBuild(): ?string
    {
        return $this->getAppEventRootData('version.build') === null ?
                null :
                (string)$this->getAppEventRootData('version.build');
    }

    /**
     * Returns the `release` form of a version number.
     *
     * The `release` form of a version number string omits
     * the build number portion. eg `1.2.3`.
     *
     * @return string|null
     */
    public function getVersionNumberRelease(): ?string
    {
        return $this->getAppEventRootData('version.release') === null ?
                null :
                (string)$this->getAppEventRootData('version.release');
    }

    /**
     * Returns an integer representation of the build number
     *
     * @return integer|null
     */
    public function getVersionNumberInt(): ?int
    {
        return $this->getAppEventRootData('version.build_int') === null ?
                null :
                (int)$this->getAppEventRootData('version.build_int');
    }
}

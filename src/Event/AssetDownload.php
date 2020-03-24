<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event;

/**
 * ** AssetDownload **
 *
 * Captures data attributes related to an asset download event and allows the
 * data to to be send to a logging target.
 *
 * Assets, in this instance, refers to software application installers
 * and digital content packs.
 *
 * Sets the following fields:
 *
 * `<ROOT ATTR>.id`
 * `<ROOT ATTR>.key`
 * `<ROOT ATTR>.file.extension`
 * `<ROOT ATTR>.file.size`
 * `<ROOT ATTR>.file.name`
 * `<ROOT ATTR>.file.type`
 * `<ROOT ATTR>.file.content_pack.host_applications`
 * `<ROOT ATTR>.file.application_installer.product_family`
 * `<ROOT ATTR>.file.application_installer.release_type`
 * `<ROOT ATTR>.file.application_installer.os.platform`
 */
class AssetDownload extends AbstractTimeSeriesEvent
{
    public function __construct()
    {
        parent::__construct();
        # The ECS `file` field defines a `type` field with various valid values.
        # https://www.elastic.co/guide/en/ecs/current/ecs-file.html
        # For our purposes here it's always `file`.
        $this->setData(self::ROOT_ATTR . '.file.type', 'file');
        # The event always starts in the `incomplete` state
        $this->setEventOutcome(self::INCOMPLETE);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'asset_download';
    }

    /**
     * Sets the internal file ID of the downloaded file.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.id`
     *
     * @param string $id
     * @return self
     */
    public function setFileId(string $id): self
    {
        return $this->setData(self::ROOT_ATTR . '.id', $id);
    }

    /**
     * Sets the internal file key of the downloaded file.
     * This is the key of the S3 object that stores the source file data.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.key`
     *
     * @param string $id
     * @return self
     */
    public function setFileKey(string $key): self
    {
        return $this->setData(self::ROOT_ATTR . '.key', $key);
    }

    /**
     * Sets the file extension of the downloaded file.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.file.extension`
     *
     * @param string $ext
     * @return self
     */
    public function setFileExtension(string $ext): self
    {
        return $this->setData(self::ROOT_ATTR . '.file.extension', $ext);
    }

    /**
     * Sets the total size (in bytes) of the downloaded file.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.file.size`
     *
     * @param integer $bytes
     * @return self
     */
    public function setFileSize(int $bytes): self
    {
        return $this->setData(self::ROOT_ATTR . '.file.size', $bytes);
    }

    /**
     * Builds structured resource attributes from raw data.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.file.name`
     * `<ROOT ATTR>.file.type`
     * `<ROOT ATTR>.file.content_pack.host_applications`
     * `<ROOT ATTR>.file.application_installer.product_family`
     * `<ROOT ATTR>.file.application_installer.release_type`
     * `<ROOT ATTR>.file.application_installer.os.platform`
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
        $this->setData(self::ROOT_ATTR . '.version', $this->getBuildNumberData($version));

        if ($resourceType === 'content-pack') {
            $this->setData(self::ROOT_ATTR . '.type', 'content_pack');
            $this->setData(self::ROOT_ATTR . '.name', $name);
            # Yeah yeah, hardcoded for now.
            $this->setData(self::ROOT_ATTR . '.content_pack.host_applications', ['Serato Studio']);
        } else {
            $type = 'application_installer';
            if ($resourceType === 'win-installer-no-corepack' || $resourceType === 'mac-installer-no-corepack') {
                $type = 'application_installer_no_content';
            }
            $this->setData(self::ROOT_ATTR . '.type', $type);
            $productFamily = $this->productShortNameToProductFamily($product);
            $this->setData(self::ROOT_ATTR . '.name', $productFamily . ' ' . $this->getVersioNumberRelease());
            $this->setData(self::ROOT_ATTR . '.application_installer.product_family', $productFamily);
            $this->setData(self::ROOT_ATTR . '.application_installer.os.platform', $this->getNormalizedOsPlatform($resourceType));
            $this->setData(self::ROOT_ATTR . '.application_installer.release_type', $releaseType);
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
    public function getVersioNumberBuild(): ?string
    {
        return $this->getData(self::ROOT_ATTR . '.version.build') === null ?
                null :
                (string)$this->getData(self::ROOT_ATTR . '.version.build');
    }

    /**
     * Returns the `release` form of a version number.
     *
     * The `release` form of a version number string omits
     * the build number portion. eg `1.2.3`.
     *
     * @return string|null
     */
    public function getVersioNumberRelease(): ?string
    {
        return $this->getData(self::ROOT_ATTR . '.version.release') === null ?
                null :
                (string)$this->getData(self::ROOT_ATTR . '.version.release');
    }

    /**
     * Returns an integer representation of the build number
     *
     * @return integer|null
     */
    public function getVersioNumberInt(): ?int
    {
        return $this->getData(self::ROOT_ATTR . '.version.build_int') === null ?
                null :
                (int)$this->getData(self::ROOT_ATTR . '.version.build_int');
    }
}

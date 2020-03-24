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
 * `<ROOT ATTR>.asset_download.id`
 * `<ROOT ATTR>.asset_download.key`
 * `<ROOT ATTR>.asset_download.file.extension`
 * `<ROOT ATTR>.asset_download.file.size`
 * `<ROOT ATTR>.asset_download.file.name`
 * `<ROOT ATTR>.asset_download.file.type`
 * `<ROOT ATTR>.asset_download.file.content_pack.host_applications`
 * `<ROOT ATTR>.asset_download.file.application_installer.product_family`
 * `<ROOT ATTR>.asset_download.file.application_installer.release_type`
 * `<ROOT ATTR>.asset_download.file.application_installer.os.platform`
 */
class AssetDownload extends AbstractTimeSeriesEvent
{
    public function __construct()
    {
        parent::__construct();
        # The ECS `file` field defines a `type` field with various valid values.
        # https://www.elastic.co/guide/en/ecs/current/ecs-file.html
        # For our purposes here it's always `file`.
        $this->setData($this->getEventDataRootAttribute() . '.file.type', 'file');
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
     * `<ROOT ATTR>.asset_download.id`
     *
     * @param string $id
     * @return self
     */
    public function setFileId(string $id): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.id', $id);
    }

    /**
     * Sets the internal file key of the downloaded file.
     * This is the key of the S3 object that stores the source file data.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.asset_download.key`
     *
     * @param string $id
     * @return self
     */
    public function setFileKey(string $key): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.key', $key);
    }

    /**
     * Sets the file extension of the downloaded file.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.asset_download.file.extension`
     *
     * @param string $ext
     * @return self
     */
    public function setFileExtension(string $ext): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.file.extension', $ext);
    }

    /**
     * Sets the total size (in bytes) of the downloaded file.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.asset_download.file.size`
     *
     * @param integer $bytes
     * @return self
     */
    public function setFileSize(int $bytes): self
    {
        return $this->setData($this->getEventDataRootAttribute() . '.file.size', $bytes);
    }

    /**
     * Builds structured resource attributes from raw data.
     *
     * Sets the following field(s):
     *
     * `<ROOT ATTR>.asset_download.file.name`
     * `<ROOT ATTR>.asset_download.file.type`
     * `<ROOT ATTR>.asset_download.file.content_pack.host_applications`
     * `<ROOT ATTR>.asset_download.file.application_installer.product_family`
     * `<ROOT ATTR>.asset_download.file.application_installer.release_type`
     * `<ROOT ATTR>.asset_download.file.application_installer.os.platform`
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
        $this->setData($this->getEventDataRootAttribute() . '.version', $this->getBuildNumberData($version));

        if ($resourceType === 'content-pack') {
            $this->setData($this->getEventDataRootAttribute() . '.type', 'content_pack');
            $this->setData($this->getEventDataRootAttribute() . '.name', $name);
            # Yeah yeah, hardcoded for now.
            $this->setData($this->getEventDataRootAttribute() . '.content_pack.host_applications', ['Serato Studio']);
        } else {
            $type = 'application_installer';
            if ($resourceType === 'win-installer-no-corepack' || $resourceType === 'mac-installer-no-corepack') {
                $type = 'application_installer_no_content';
            }
            $this->setData($this->getEventDataRootAttribute() . '.type', $type);
            $productFamily = $this->productShortNameToProductFamily($product);
            $this->setData($this->getEventDataRootAttribute() . '.name', $productFamily . ' ' . $this->getVersioNumberRelease());
            $this->setData($this->getEventDataRootAttribute() . '.application_installer.product_family', $productFamily);
            $this->setData($this->getEventDataRootAttribute() . '.application_installer.os.platform', $this->getNormalizedOsPlatform($resourceType));
            $this->setData($this->getEventDataRootAttribute() . '.application_installer.release_type', $releaseType);
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
        return $this->getData($this->getEventDataRootAttribute() . '.version.build') === null ?
                null :
                (string)$this->getData($this->getEventDataRootAttribute() . '.version.build');
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
        return $this->getData($this->getEventDataRootAttribute() . '.version.release') === null ?
                null :
                (string)$this->getData($this->getEventDataRootAttribute() . '.version.release');
    }

    /**
     * Returns an integer representation of the build number
     *
     * @return integer|null
     */
    public function getVersioNumberInt(): ?int
    {
        return $this->getData($this->getEventDataRootAttribute() . '.version.build_int') === null ?
                null :
                (int)$this->getData($this->getEventDataRootAttribute() . '.version.build_int');
    }
}

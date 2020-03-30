<?php
declare(strict_types=1);

namespace Serato\AppEvents\Event;

use Serato\AppEvents\Exception\InvalidDataValueException;

/**
 * ** AbstractDataContainer **
 *
 * Abstract class that all data containers inherit from.
 *
 * Includes helper functions for converting disprate source data into common values.
 */
abstract class AbstractDataContainer
{
    public const LICENSE_TERM_PERMANENT = 'permanent';
    public const LICENSE_TERM_SUBSCRIPTION = 'subscription';
    public const LICENSE_TERM_TIMELIMITED = 'timelimited';

    /* @var array */
    private $data = [];

    /* @var array */
    private $pathedData = [];

    /**
     * Returns the entire event data array
     *
     * @return array
     */
    public function get(): array
    {
        return $this->data;
    }

    /**
     * Returns data for a specified path
     *
     * @param string $path
     * @return null|mixed
     */
    protected function getData(string $path)
    {
        return isset($this->pathedData[$path]) ? $this->pathedData[$path] : null;
    }

    /**
     * Sets data to a specified path
     *
     * @param string $path
     * @param mixed $item
     * @return mixed
     */
    protected function setData(string $path, $item)
    {
        $multiItem = false;
        if (is_array($item)) {
            foreach (array_keys($item) as $k) {
                if (is_string($k)) {
                    $multiItem = true;
                    continue;
                }
            }
        }
        $pathArray = explode('.', $path);
        $this->pathedData[$path] = $item;
        $this->set($pathArray, $this->data, $item);
        if ($multiItem) {
            foreach ($item as $k => $v) {
                $this->pathedData[$path . '.' . $k] = $v;
                $this->set(array_merge($pathArray, [$k]), $this->data, $v);
            }
        }
        return $this;
    }

    private function set(array $path, &$data, $item): void
    {
        if (count($path) > 0) {
            $k = array_shift($path);
            if (count($path) === 0) {
                $data[$k] = $item;
            } else {
                if (!isset($data[$k]) || !is_array($data[$k])) {
                    $data[$k] = [];
                }
                $this->set($path, $data[$k], $item);
            }
        }
    }

    /**
     * Builds formatted and structured version number data from a raw version number string
     *
     * @param string $raw
     * @return array
     */
    protected function getBuildNumberData(string $raw): array
    {
        $data = [];

        $vBits = explode('.', $raw);

        $major = (string)$vBits[0];
        $minor = (string)(isset($vBits[1]) ? $vBits[1] : '0');
        $point = (string)(isset($vBits[2]) ? $vBits[2] : '0');
        $build = (string)(isset($vBits[3]) ? $vBits[3] : '0');

        $data['release'] = $major . '.' . $minor . '.' . $point;
        $data['build'] = $major . '.' . $minor . '.' . $point . '.' . $build;

        $data['build_int'] = intval(
            $major .
            str_pad($minor, 2, '0', STR_PAD_LEFT) .
            str_pad($point, 3, '0', STR_PAD_LEFT) .
            str_pad(substr($build, 0, 6), 6, '0', STR_PAD_LEFT)
        );

        return $data;
    }

    /**
     * Converts short names for Serato products into their full name
     *
     * @param string $product
     * @return string
     */
    protected function productShortNameToProductFamily(string $product): string
    {
        switch ($product) {
            case 'scratchlive':
                return 'Scratch Live';
            case 'video-sl':
                return 'Video-SL';
            case 'itch':
                return 'ITCH';
            case 'raneseries-equalizers':
                return 'Rane Series Equalizers';
            case 'raneseries-dynamics':
                return 'Rane Series Dynamics';
            case 'pitchntime-pro':
                return 'Pitch n Time Pro';
            case 'pitchntime-le':
                return 'Pitch n Time LE';
            case 'pitchntime-fe':
                return 'Pitch n Time FE';
            case 'raneseries-graphiceq':
                return 'Rane Series Graphic EQ';
            case 'raneseries-parametriceq':
                return 'Rane Series Parametric EQ';
            case 'dj-intro':
                return 'Serato DJ Intro';
            case 'video':
                return 'Serato Video';
            case 'dj':
                return 'Serato DJ Pro';
            case 'serato_sample':
            case 'sample':
                return 'Serato Sample';
            case 'dj_lite':
            case 'dj-lite':
                return 'Serato DJ Lite';
            case 'serato_studio':
            case 'studio':
                return 'Serato Studio';
            case 'wailshark':
                return 'Wailshark';
        }
        return '';
    }

    /**
     * Returns a normalized OS platform string
     *
     * @param string $str
     * @return string
     */
    protected function getNormalizedOsPlatform(string $str): string
    {
        switch (strtolower($str)) {
            case 'win':
            case 'windows':
            case 'win-installer':
            case 'win-32-installer':
            case 'win-installer-no-corepack':
                return 'Windows';
            case 'mac':
            case 'macos':
            case 'osx':
            case 'os x':
            case 'mac-installer':
            case 'mac-32-installer':
            case 'mac-installer-no-corepack':
                return 'macOS';
            default:
                return $str;
        }
    }

    /**
     * Validates a value against a list of accepted values
     *
     * @param mixed $val
     * @param array $accepted
     * @param string $methodName
     * @param string|null $paramName
     * @return void
     * @throws InvalidDataValueException
     */
    protected function validateDataValue($val, array $accepted, string $methodName, ?string $paramName = null): void
    {
        if (!in_array($val, $accepted)) {
            throw new InvalidDataValueException(
                "Invalid value `" . $val . "` provided to " .
                is_null(null) ? '' : "`" . $paramName . "` parameter in " .
                "method `" . $methodName . "`.\n" .
                "Accepted values are `" . implode("`, `", $accepted) . "`."
            );
        }
    }
}

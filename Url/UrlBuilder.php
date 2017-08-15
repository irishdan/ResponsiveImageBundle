<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Url;

use IrishDan\ResponsiveImageBundle\FileSystem\PrimaryFileSystemWrapper;
use League\Flysystem\AdapterInterface;
use Psr\Log\InvalidArgumentException;

/**
 * Class UrlBuilder
 *
 * @package IrishDan\ResponsiveImageBundle\Url
 */
class UrlBuilder
{
    /**
     * @var \League\Flysystem\FilesystemInterface|null
     */
    private $fileSystem;
    /**
     * @var array
     */
    private $config;
    /**
     * @var array
     */
    private $adapterUrlEncoders = [];
    /**
     * @var array
     */
    private $adapterUrlMappings = [];

    /**
     * @param string $adapterType
     *
     * @return array
     */
    public function getAdapterUrlMappings($adapterType = '')
    {
        if (empty($adapterType)) {
            return $this->adapterUrlMappings;
        }

        return empty($this->adapterUrlMappings[$adapterType]) ? null : $this->adapterUrlMappings[$adapterType];
    }

    public function setAdapterUrlMappings($adapterType, $data)
    {
        if (!is_string($adapterType)) {
            throw new InvalidArgumentException(
                'Adapter type must be a string'
            );
        }

        $this->adapterUrlMappings[$adapterType] = $data;
    }

    /**
     * UrlBuilder constructor.
     *
     * @param PrimaryFileSystemWrapper|null $PrimaryFileSystemWrapper
     * @param array|null                    $config
     */
    public function __construct(PrimaryFileSystemWrapper $PrimaryFileSystemWrapper = null, array $config = null)
    {
        if (!empty($PrimaryFileSystemWrapper)) {
            $this->fileSystem = $PrimaryFileSystemWrapper->getFileSystem();
        }
        $this->config = $config;
    }

    /**
     * @param                     $key
     * @param UrlEncoderInterface $encoder
     */
    public function setAdapterUrlEncoder($key, UrlEncoderInterface $encoder)
    {
        $this->adapterUrlEncoders[$key] = $encoder;
    }

    /**
     * @param        $relativeFilePath
     * @param array  $urlData
     *
     * @return string
     * @internal param string $adapterUrlData
     */
    public function filePublicUrl($relativeFilePath, array $urlData = [])
    {
        // @TODO: $urlData could be collected when
        // Either the data needed to build the url is passed in adapterUrlData
        // Or it should be derived from the Adapter

        // Build path from the provided data if it exists
        if (!empty($urlData)) {
            return $this->formatAsUrl($urlData, $relativeFilePath);
        }

        // Build the url from any mappings provided.
        $adapterType = $this->getAdapterTypeFromFilesystem();
        if (!empty($this->getAdapterUrlMappings($adapterType))) {
            return $this->formatAsUrl($urlData, $relativeFilePath);
        }

        // Build the path from adapter.
        // Most adaptors don't have any direct method to do this.
        $urlData = $this->getUrlFromFileSystem();
        if (!empty($urlData)) {
            return $this->formatAsUrl($urlData, $relativeFilePath);
        }

        // Use a fallback from config.
        if (!empty($this->config['default_url'])) {
            return $this->formatAsUrl($this->config['default_url'], $relativeFilePath);
        }

        // If all of the above methods fail just return the path.
        // perhaps it being generated elsewhere!!

        return $relativeFilePath;
    }

    private function getAdapterTypeFromFilesystem()
    {
        $adapter = $this->fileSystem->getAdapter();

        return $this->getAdapterType($adapter);
    }

    /**
     * @param $base
     * @param $path
     *
     * @return string
     */
    protected function formatAsUrl($base, $path)
    {
        // @TODO: $base could also be an array.

        $url = $base . '/' . trim($path, '/');

        // Check it the protocol is included.
        $urlBits = explode('://', $url);
        if ($urlBits[0] == 'http' || $urlBits[0] == 'https') {
            $urlPrefix = $urlBits[0] . '://';
            $urlPath   = $urlBits[1];
        }
        else {
            $urlPrefix = '/';
            $urlPath   = $url;
        }

        // Format the url part to ensure its correct.
        $urlParts = explode('/', $urlPath);
        foreach ($urlParts as $index => $part) {
            $part = trim($part);
            if (empty($part)) {
                unset($urlParts[$index]);
            }
        }
        $urlPath = implode('/', $urlParts);

        return $urlPrefix . $urlPath;
    }

    /**
     * @return string
     * @internal param array $data
     *
     */
    protected function getUrlFromFileSystem()
    {
        $path        = false;
        $adapter     = $this->fileSystem->getAdapter();
        $adapterType = $this->getAdapterType($adapter);

        if (!empty($this->adapterUrlEncoders[$adapterType])) {
            $encoder = $this->adapterUrlEncoders[$adapterType];
            $path    = $encoder->getUrl($adapter, $this->config);
        }

        return $path;
    }

    /**
     * @param AdapterInterface $adapter
     *
     * @return mixed
     */
    protected function getAdapterType(AdapterInterface $adapter)
    {
        $class          = get_class($adapter);
        $namespaceArray = explode("\\", $class);

        return array_pop($namespaceArray);
    }
}
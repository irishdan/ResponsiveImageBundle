<?php

namespace IrishDan\ResponsiveImageBundle\Url;

use IrishDan\ResponsiveImageBundle\FileSystem\PrimaryFileSystemWrapper;
use League\Flysystem\AdapterInterface;

/**
 * Class UrlBuilder
 *
 * @package IrishDan\ResponsiveImageBundle\Url
 */
class UrlBuilder
{
    private $fileSystem;
    private $config;
    private $adapterUrlEncoders = [];

    public function __construct(PrimaryFileSystemWrapper $PrimaryFileSystemWrapper = null, array $config = null)
    {
        if (!empty($PrimaryFileSystemWrapper)) {
            $this->fileSystem = $PrimaryFileSystemWrapper->getFileSystem();
        }
        $this->config = $config;
    }

    public function adapterUrlEncoder($key, UrlEncoderInterface $encoder)
    {
        $this->adapterUrlEncoders[$key] = $encoder;
    }

    public function filePublicUrl($relativeFilePath, $adapterUrlData = '')
    {
        if (!empty($adapterUrlData)) {
            $urlBase = $this->getUrlDataFromFileSystem(unserialize($adapterUrlData));
        }
        else {
            $urlBase = $this->getUrlDataFromFileSystem();
        }

        return $this->formatAsUrl($urlBase, $relativeFilePath);
    }

    protected function formatAsUrl($base, $path)
    {
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

    protected function getUrlDataFromFileSystem($data = [])
    {
        $path = '/';
        if (empty($data)) {
            $adapter     = $this->fileSystem->getAdapter();
            $adapterType = $this->getAdapterType($adapter);
        }
        else {
            $adapterType = $data['adapter'];
        }

        if (!empty($this->adapterUrlEncoders[$adapterType])) {
            $encoder = $this->adapterUrlEncoders[$adapterType];

            if (!empty($adapter)) {
                $path = $encoder->getUrlFromAdapter($adapter, $this->config);
            }
            else {
                $path = $encoder->getUrlFromData($data, $this->config);
            }
        }

        return $path;
    }

    protected function getAdapterType(AdapterInterface $adapter)
    {
        $class          = get_class($adapter);
        $namespaceArray = explode("\\", $class);

        return array_pop($namespaceArray);
    }
}
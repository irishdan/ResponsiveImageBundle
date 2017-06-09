<?php

namespace IrishDan\ResponsiveImageBundle;

use IrishDan\ResponsiveImageBundle\FileSystem\FileSystemFactory;
use League\Flysystem\AdapterInterface;

class UrlBuilder
{
    private $fileSystem;

    public function __construct(FileSystemFactory $fileSystemFactory = null)
    {
        if (!empty($fileSystemFactory)) {
            $this->fileSystem = $fileSystemFactory->getFileSystem();
        }
    }

    public function filePublicUrl($relativeFilePath, $adapterData = '')
    {
        // @TODO:
        if (!empty($adapterData)) {
            $pathArray = $this->getUrlDataFromFileSystem(unserialize($adapterData));
        } else {
            $pathArray = $this->getUrlDataFromFileSystem();
        }

        list($protocol, $urlBase) = $pathArray;

        return $this->formatAsUrl($protocol, $urlBase, $relativeFilePath);
    }

    protected function formatAsUrl($protocol = '', $base = '', $path)
    {
        $url = $base . '/' . $path;
        $urlParts = explode('/', $url);

        foreach ($urlParts as $index => $part) {
            $part = trim($part);
            if (empty($part)) {
                unset($urlParts[$index]);
            }
        }

        $url = implode('/', $urlParts);
        if (!empty($protocol)) {
            return $protocol . '://' . $url;
        }

        return '/' . $url;
    }

    protected function getUrlDataFromFileSystem($data = [])
    {
        if (empty($data)) {
            /* */
            $adapter = $this->fileSystem->getAdapter();
            $adapterType = $this->getAdapterType($adapter);
        } else {
            $adapterType = $data['adapter'];
        }

        switch ($adapterType) {
            case 'AwsS3Adapter':
                if (!empty($adapter)) {
                    $prefix = $adapter->getPathPrefix();
                    $bucket = $adapter->getBucket();
                    $region = $adapter->getClient()->getRegion();

                    // @TODO: Where does protocol come from
                    $path = ['https', $this->buildAWSPath($prefix, $region, $bucket)];
                } else {
                    $path = ['https', $this->buildAWSPath($data['prefix'], $data['region'], $data['bucket'])];
                }

                break;

            case 'Local':
                // @TODO: Should return the path relative to web directory
                $path = ['', 'test/images'];
                break;

            default:
                $path = ['', '/'];
                break;
        }

        return $path;
    }

    protected function buildAWSPath($prefix = '', $region = 'region', $bucket = 'bucket')
    {
        $url = 's3-' . $region . '.amazonaws.com/' . $bucket . '/' . $prefix . '/';

        return $url;
    }

    protected function getAdapterType(AdapterInterface $adapter)
    {
        $class = get_class($adapter);
        $namespaceArray = explode("\\", $class);

        return array_pop($namespaceArray);
    }
}
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
    public function adapterUrlEncoder($key, UrlEncoderInterface $encoder)
    {
        $this->adapterUrlEncoders[$key] = $encoder;
    }

    /**
     * @param        $relativeFilePath
     * @param string $adapterUrlData
     *
     * @return string
     */
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

    /**
     * @param $base
     * @param $path
     *
     * @return string
     */
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

    /**
     * @param array $data
     *
     * @return string
     */
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
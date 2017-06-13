<?php


namespace IrishDan\ResponsiveImageBundle\Url;

use League\Flysystem\FilesystemInterface;

class LocalAdapterUrlEncoder implements UrlEncoderInterface
{
    public function getUrlFromAdapter($adapter, array $config = null)
    {
        $path = empty($config['image_directory']) ? 'image' : $config['image_directory'];

        return $path;
    }

    public function getUrlFromData($data, array $config = null)
    {
        // TODO: Implement getUrlFromData() method.
    }
}
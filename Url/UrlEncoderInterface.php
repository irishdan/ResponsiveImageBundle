<?php


namespace IrishDan\ResponsiveImageBundle\Url;


use League\Flysystem\FilesystemInterface;

interface UrlEncoderInterface
{
    public function getUrlFromAdapter($adapter, array $config = null);

    public function getUrlFromData($data, array $config = null);
}
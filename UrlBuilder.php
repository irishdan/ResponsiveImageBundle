<?php

namespace IrishDan\ResponsiveImageBundle;

class UrlBuilder
{
    private $fileSystem;

    public function __construct($fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function getFileUrl($relativeFilePath)
    {
        $urlBase = $this->getFileSystemBaseUrl();

        return $this->formatAsUrl($urlBase, $relativeFilePath);
    }

    protected function formatAsUrl($base, $path)
    {
        // @TODO: break it up and re build to remove ay inconsistencies.
        return '/' . $base . '/' . $path;
    }

    protected function getFileSystemBaseUrl()
    {
        // @TODO:
        return 'how/are/we/going/to-get/it';
    }
}
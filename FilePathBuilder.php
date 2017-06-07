<?php

namespace IrishDan\ResponsiveImageBundle;

class FilePathBuilder
{
    private $styles = [];

    public function __construct($configuredStyles)
    {
        $this->styles = $configuredStyles;
    }

    public function getPathsArray($relativeFilePath, $styles = [])
    {
        // given the original, and possible a set of styles returns all styled paths
    }

    public function getOriginalRelativePath($relativeStyledFilePath)
    {
        // given a styled path, returns the original
    }
}
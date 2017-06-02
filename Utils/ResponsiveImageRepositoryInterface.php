<?php

namespace IrishDan\ResponsiveImageBundle\Utils;

Interface ResponsiveImageRepositoryInterface
{
    public function findImageFromFilename($filename);
}
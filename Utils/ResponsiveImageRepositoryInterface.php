<?php

namespace ResponsiveImageBundle\Utils;

Interface ResponsiveImageRepositoryInterface
{
    public function findImageFromFilename($filename);
}
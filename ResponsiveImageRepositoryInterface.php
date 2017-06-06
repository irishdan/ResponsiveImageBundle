<?php

namespace IrishDan\ResponsiveImageBundle;

Interface ResponsiveImageRepositoryInterface
{
    public function findImageFromFilename($filename);
}
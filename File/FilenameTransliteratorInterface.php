<?php

namespace IrishDan\ResponsiveImageBundle\File;

interface FilenameTransliteratorInterface
{
    public function transliterate($filename);
}
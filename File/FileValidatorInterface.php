<?php

namespace IrishDan\ResponsiveImageBundle\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileValidatorInterface
{
    public function validate(UploadedFile $file);
}
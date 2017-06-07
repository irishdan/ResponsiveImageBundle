<?php

namespace IrishDan\ResponsiveImageBundle\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileValidator implements FileValidatorInterface
{
    public function validate(UploadedFile $file)
    {
        return true;
    }
}
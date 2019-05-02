<?php

namespace IrishDan\ResponsiveImageBundle\Exception;

use Throwable;

class ImageNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if ($message) {
            $message = 'Unable to find an image with file name ' . $message;
        }
        else {
            $message = 'Unable to find an image' ;
        }

        parent::__construct($message, $code, $previous);
    }
}
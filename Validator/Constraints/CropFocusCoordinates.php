<?php

namespace IrishDan\ResponsiveImageBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CropFocusCoordinates extends Constraint
{
    public $message = 'The string {{ string }} is not a valid Crop Focus coordinate set';
}
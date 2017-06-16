<?php

namespace IrishDan\ResponsiveImageBundle\Validator\Constraints;

use IrishDan\ResponsiveImageBundle\ImageProcessing\CoordinateGeometry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Twig\Extension\CoreExtension;

class CropFocusCoordinatesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // Check it is in the format: 0,0,0,0:0,0,0,0
        $testValue = str_replace(' ', '', $value);
        if (!preg_match(
            '/^(\d+),(\d+),(\d+),(\d+):(\d+),(\d+),(\d+),(\d+)$/',
            $testValue,
            $matches
        )
        ) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ string }}', $value)
                          ->addViolation()
            ;
        }
        else {
            // Check the focus rectangle is inside the crop rectangle.
            $cropFocusCoordinates = explode(':', $testValue);
            $crop                 = explode(',', $cropFocusCoordinates[0]);
            $focus                = explode(',', $cropFocusCoordinates[1]);

            // Add constraint generated entity.
            $geometry = new CoordinateGeometry($crop[0], $crop[1], $crop[2], $crop[3]);

            if (!$geometry->isInside($focus[0], $focus[1], $focus[2], $focus[3])) {
                $this->context->buildViolation($constraint->message)
                              ->setParameter('{{ string }}', $value)
                              ->addViolation()
                ;
            }
        }
    }
}
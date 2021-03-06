<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Validator\Constraints;

use IrishDan\ResponsiveImageBundle\ImageProcessing\CoordinateGeometry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CropFocusCoordinatesValidator
 *
 * @package IrishDan\ResponsiveImageBundle\Validator\Constraints
 */
class CropFocusCoordinatesValidator extends ConstraintValidator
{
    /**
     * Validate a crop focus coordinate string to ensure the syntax is correct
     * and to ensure that the focus rectangle inside the crop rectangle
     *
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $valid = true;

        // Check it is in the format: 0,0,0,0:0,0,0,0
        $testValue = str_replace(' ', '', $value);
        if (!preg_match('/^(\d+),(\d+),(\d+),(\d+):(\d+),(\d+),(\d+),(\d+)$/', $testValue, $matches)) {
            $valid = false;
        }
        else {
            // Check the focus rectangle is inside the crop rectangle.
            $cropFocusCoordinates = explode(':', $testValue);
            $crop                 = explode(',', $cropFocusCoordinates[0]);
            $focus                = explode(',', $cropFocusCoordinates[1]);

            // Add constraint generated entity.
            $geometry = new CoordinateGeometry($crop[0], $crop[1], $crop[2], $crop[3]);

            if (!$geometry->isInside($focus[0], $focus[1], $focus[2], $focus[3])) {
                $valid = false;
            }
        }

        if (!$valid) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ string }}', $value)
                          ->addViolation()
            ;
        }
    }
}
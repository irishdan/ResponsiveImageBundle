<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Tests\Validator\Constraints;


use IrishDan\ResponsiveImageBundle\Validator\Constraints\CropFocusCoordinates;
use IrishDan\ResponsiveImageBundle\Validator\Constraints\CropFocusCoordinatesValidator;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CropFocusCoordinatesValidatorTest extends ConstraintValidatorTestCase
{
    protected function setUp()
    {
        $this->group        = 'MyGroup';
        $this->metadata     = null;
        $this->object       = null;
        $this->value        = 'InvalidValue';
        $this->root         = 'root';
        $this->propertyPath = 'property.path';

        // Initialize the context with some constraint so that we can
        // successfully build a violation.
        $this->constraint = new NotNull();

        $this->context   = $this->createContext();
        $this->validator = $this->createValidator();
        $this->validator->initialize($this->context);

        $this->setDefaultTimezone('UTC');
    }

    protected function createValidator()
    {
        return new CropFocusCoordinatesValidator();
    }

    public function testValidCoordinates()
    {
        $this->validator->validate('10, 20, 80, 90: 11, 21, 79, 90', new CropFocusCoordinates());

        $this->assertNoViolation();
    }

    public function testNullIsInValid()
    {
        $this->validator->validate(null, new CropFocusCoordinates());

        $this->assertSame(
            1,
            $violationsCount = count($this->context->getViolations()),
            sprintf('0 violation expected. Got %u.', $violationsCount)
        );
    }

    public function testFocusIsLargerThanCrop()
    {
        $this->validator->validate('10, 20, 80, 90: 9, 21, 79, 90', new CropFocusCoordinates());

        $this->assertSame(
            1,
            $violationsCount = count($this->context->getViolations()),
            sprintf('0 violation expected. Got %u.', $violationsCount)
        );
    }

    public function testNoFocusIsSet()
    {
        // @TODO: We need to allow for no focus to be set
        $this->validator->validate('10, 20, 80, 90:', new CropFocusCoordinates());

        $this->assertSame(
            1,
            $violationsCount = count($this->context->getViolations()),
            sprintf('0 violation expected. Got %u.', $violationsCount)
        );
    }
}
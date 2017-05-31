<?php

namespace ResponsiveImageBundle\Tests\Utils;

use ResponsiveImageBundle\Tests\ResponsiveImageTestCase;
use ResponsiveImageBundle\Utils\FocusCropDataCalculator;

class FocusCropDataCalculatorTest extends ResponsiveImageTestCase
{
    public function testGetFocusCropData()
    {
        $focusOffsetFinder = new FocusCropDataCalculator(
            [10, 10, 90, 90],
            [40, 40, 60, 60],
            30,
            60
        );

        $focusCropData = $focusOffsetFinder->getFocusCropData();

        $this->assertArrayHasKey('width', $focusCropData);
        $this->assertArrayHasKey('height', $focusCropData);
        $this->assertArrayHasKey('x', $focusCropData);
        $this->assertArrayHasKey('y', $focusCropData);

        $this->assertTrue($focusCropData['width'] < 80);
        $this->assertTrue($focusCropData['height'] >= 80);
        $this->assertTrue($focusCropData['x'] >= 10);
        $this->assertTrue($focusCropData['y'] >= 10);
    }

    public function testGetFocusCropDataWithVerticalStyle()
    {
        $focusOffsetFinder = new FocusCropDataCalculator(
            [10, 10, 90, 90],
            [40, 40, 60, 60],
            30,
            60
        );

        $focusCropData = $focusOffsetFinder->getFocusCropData();

        // for vertical style
        $this->assertTrue($focusCropData['width'] < $focusCropData['height']);
        $this->assertEquals($focusCropData['height'], 80);
        $this->assertTrue($focusCropData['x'] > 10);
        $this->assertTrue($focusCropData['y'] == 10);
    }

    public function testGetFocusCropDataHorizontalStyle()
    {
        $focusOffsetFinder = new FocusCropDataCalculator(
            [10, 10, 90, 90],
            [40, 40, 60, 60],
            60,
            30
        );

        $focusCropData = $focusOffsetFinder->getFocusCropData();

        // for horizontal style
        $this->assertTrue($focusCropData['width'] > $focusCropData['height']);
        $this->assertEquals($focusCropData['width'], 80);
        $this->assertTrue($focusCropData['x'] == 10);
        $this->assertTrue($focusCropData['y'] > 10);
    }

    public function testGetFocusCropDataStyleAnCropMatch()
    {
        $focusOffsetFinder = new FocusCropDataCalculator(
            [10, 10, 90, 90],
            [40, 40, 60, 60],
            60,
            60
        );

        $focusCropData = $focusOffsetFinder->getFocusCropData();

        // for horizontal style
        $this->assertTrue($focusCropData['width'] == $focusCropData['height']);
        $this->assertEquals($focusCropData['width'], 80);
        $this->assertTrue($focusCropData['x'] == 10);
        $this->assertTrue($focusCropData['y'] == 10);
    }
}

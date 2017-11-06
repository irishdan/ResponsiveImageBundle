<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Tests\ImageProcessing;

use IrishDan\ResponsiveImageBundle\ImageProcessing\FocusCropDataCalculator;
use IrishDan\ResponsiveImageBundle\Tests\ResponsiveImageTestCase;

class FocusCropDataCalculatorTest extends ResponsiveImageTestCase
{
    public function testGetFocusCropDataIsValid()
    {
        $testData = [
            [
                'crop_coordinates'  => [10, 10, 100, 100],
                'focus_coordinates' => [10, 10, 90, 90],
            ],
            [
                'crop_coordinates'  => [283, 397, 991, 1289],
                'focus_coordinates' => [491, 514, 659, 709]
            ],
            [
                'crop_coordinates'  => [227, 291, 2826, 2114],
                'focus_coordinates' => [1209, 634, 1743, 1195],
            ],
        ];

        $testStyles = [
            [30, 60],
            [40, 50],
            [170, 240],
            [240, 170],
        ];

        foreach ($testData as $imageCoordinates) {
            foreach ($testStyles as $style) {
                $focusOffsetFinder = new FocusCropDataCalculator(
                    $imageCoordinates['crop_coordinates'],
                    $imageCoordinates['focus_coordinates'],
                    $style[0],
                    $style[1]
                );

                $focusCropData = $focusOffsetFinder->getFocusCropData();

                $this->assertArrayHasKey('width', $focusCropData);
                $this->assertArrayHasKey('height', $focusCropData);
                $this->assertArrayHasKey('x', $focusCropData);
                $this->assertArrayHasKey('y', $focusCropData);

                $width = round($imageCoordinates['crop_coordinates'][2]) - round($imageCoordinates['crop_coordinates'][0]); // x2 - x1
                $height = round($imageCoordinates['crop_coordinates'][3]) - round($imageCoordinates['crop_coordinates'][1]); // y2 - y1

                $message = 'Too wide: ' . $focusCropData['width'] . '<=' . $width;
                $this->assertTrue($focusCropData['width'] <= $width, $message);

                $message = 'Too high: ' . (int)round($focusCropData['height']) . ' <= ' . $height;
                $this->assertTrue((int)round($focusCropData['height']) <= (int)$height, $message);

                $this->assertTrue($focusCropData['x'] >= $imageCoordinates['crop_coordinates'][0]); // x1
                $this->assertTrue($focusCropData['y'] >= $imageCoordinates['crop_coordinates'][1]); // y1
            }
        }
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

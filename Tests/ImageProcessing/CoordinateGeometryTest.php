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

use IrishDan\ResponsiveImageBundle\ImageProcessing\CoordinateGeometry;
use PHPUnit\Framework\TestCase;

class CoordinateGeometryTest extends TestCase
{
    protected $geometry;

    public function setUp()
    {
        $this->geometry = new CoordinateGeometry(10, 20, 90, 100);
    }

    public function testAxisLength()
    {
        $this->assertEquals(80, $this->geometry->axisLength('x'));
        $this->assertEquals(80, $this->geometry->axisLength('y'));

        $this->geometry->setPoints(283, 397, 991, 1289);
        $this->assertEquals(708, $this->geometry->axisLength('x'));
        $this->assertEquals(892, $this->geometry->axisLength('y'));
    }

    public function testScaleSize()
    {
        // Scale with same proportions
        $scaledSize = $this->geometry->scaleSize(40, 40);

        $this->assertEquals(40, $scaledSize['width']);
        $this->assertEquals(40, $scaledSize['height']);

        // Scale with longer width
        $scaledSize = $this->geometry->scaleSize(80, 40);

        $this->assertEquals(40, $scaledSize['width']);
        $this->assertEquals(40, $scaledSize['height']);

        // Scale with longer height
        $scaledSize = $this->geometry->scaleSize(40, 80);

        $this->assertEquals(40, $scaledSize['width']);
        $this->assertEquals(40, $scaledSize['height']);
    }

    public function testAspectRatio()
    {
        $this->assertEquals(1, $this->geometry->aspectRatio());
        $this->assertEquals(2, $this->geometry->aspectRatio(50, 100));
    }

    public function testIsInside()
    {
        $this->assertTrue($this->geometry->isInside(11, 21, 89, 99));
        $this->assertTrue($this->geometry->isInside(10, 20, 90, 100));
        // One coordinate falls outside
        $this->assertFalse($this->geometry->isInside(9, 21, 89, 99));
        $this->assertFalse($this->geometry->isInside(11, 19, 89, 99));
        $this->assertFalse($this->geometry->isInside(11, 21, 91, 99));
        $this->assertFalse($this->geometry->isInside(11, 21, 89, 101));
    }

    public function testRoundAll()
    {
        $data = [
            'key1' => 3.870000007,
            'key2' => .9000099993,
        ];

        $rounded = $this->geometry->roundAll($data);

        $this->assertEquals(4, $rounded['key1']);
        $this->assertEquals(1, $rounded['key2']);
    }
}

<?php

class ImagerTest extends PHPUnit_Framework_TestCase
{
    public function testTruth() {
        $this->assertTrue(TRUE);
    }

    public function testFalse() {
        $this->assertFalse(FALSE);
    }

    public function myProvider() {
        return array(
            array(7, 3, 4),
            array(5, 1, 4),
            array(2, 0, 2),
            array(12, 11, 2),
        );
    }

    /**
     * @dataProvider myProvider
     */
    public function testEquals($expected, $value1, $value2) {
        $actual = $value1 + $value2;
        $this->assertEquals($expected, $actual, 'Failed Message');
    }

    public function testInstanceOf() {
        $class = New stdClass();

        $this->assertInstanceOf(
          'stdClass', $class
        );
    }

    public function testArrayContains() {
        $array = [1, 2, 3];
        $this->assertContains(
            2, $array
        );
    }

    public function testArrayNotContains() {
        $array = [1, 2, 3];
        $this->assertNotContains(
            5, $array
        );
    }

    public function testIsNull() {
        $this->assertNull(null);
    }

    public function testNotNull() {
        $this->assertNotNull(100);
    }
}

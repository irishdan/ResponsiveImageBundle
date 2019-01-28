<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Config\FileLocator;

class ImageEntityClassLocatorTest extends TestCase
{
    public function getBundleDirectory()
    {
        return $directory = __DIR__ . '/../';
    }

    public function testItCanFindTheClassWhenPresent()
    {
        // Mock the FileLocator to return the mock of the repository
        $fileLocator = $this->getMockBuilder(FileLocator::class)
                            ->disableOriginalConstructor()
                            ->getMock();

        $fileLocator->expects($this->any())
                    ->method('locate')
                    ->will($this->returnValue($this->getBundleDirectory()));

        $bundles = [
            'ResponsiveImageBundle' => 'IrishDan\ResponsiveImageBundle\ResponsiveImageBundle',
            'TwigBundle'            => "Symfony\Bundle\TwigBundle\TwigBundle",
        ];

        $locator = new ImageEntityClassLocator($bundles, $fileLocator);

        $locator->setEntityDirectory('Tests/Entity');

        $this->assertEquals('IrishDan\ResponsiveImageBundle\Tests\Entity\TestImage', $locator->getClassName());
    }

    public function testItCantFindTheClassWhenNotPresent()
    {
        // Mock the EntityManager to return the mock of the repository
        $fileLocator = $this->getMockBuilder(FileLocator::class)
                            ->disableOriginalConstructor()
                            ->getMock();


        $fileLocator->expects($this->any())
                    ->method('locate')
                    ->will($this->returnValue($this->getBundleDirectory()));

        $bundles = [
            'ResponsiveImageBundle' => 'IrishDan\ResponsiveImageBundle\ResponsiveImageBundle',
            'TwigBundle'            => 'Symfony\Bundle\TwigBundle\TwigBundle',
        ];

        $locator = new ImageEntityClassLocator($bundles, $fileLocator);

        $this->assertNull($locator->getClassName());
    }
}

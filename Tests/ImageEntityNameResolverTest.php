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
use Symfony\Component\Cache\Simple\FilesystemCache;

class ImageEntityNameResolverTest extends TestCase
{
    protected $cache;

    public function setUp()
    {
        // Clear the cache
        $this->cache = new FilesystemCache();
        $this->cache->delete('responsive_image.image_entity');
    }

    public function testReturnsTheLocatedClassName()
    {
        $fileLocator = $this->getMockBuilder(ImageEntityClassLocator::class)
                            ->disableOriginalConstructor()
                            ->getMock();

        $fileLocator->expects($this->once())
                    ->method('getClassName')
                    ->will($this->returnValue('This\Is\Located'));

        $nameResolver = new ImageEntityNameResolver($fileLocator);

        $this->assertEquals('This\Is\Located', $nameResolver->getClassName());
    }

    public function testConfiguredValueTrumpsAll()
    {
        // Mock the ImageEntityClassLocator to return the mock of the repository
        $fileLocator = $this->getMockBuilder(ImageEntityClassLocator::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        $fileLocator->expects($this->never())->method('getClassName');

        $nameResolver = new ImageEntityNameResolver($fileLocator, 'This\Is\Configured');

        $this->assertEquals('This\Is\Configured', $nameResolver->getClassName());
    }
}

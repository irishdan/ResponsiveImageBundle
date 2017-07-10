<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Tests\File;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use IrishDan\ResponsiveImageBundle\File\FileToObject;
use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use IrishDan\ResponsiveImageBundle\ResponsiveImageRepositoryInterface;
use IrishDan\ResponsiveImageBundle\Tests\Entity\TestImage;

class FileToObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testGetObjectFromFileName()
    {
        // Mock the repository so it returns the mock of the Image repository.
        $imageRepository = $this->getMockBuilder(ResponsiveImageRepositoryInterface::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $imageRepository->expects($this->any())
                        ->method('findImageFromFilename')
                        ->will(
                            $this->returnCallback(
                                function ($filename) {
                                    $image = new TestImage();
                                    if ($filename === $image->getPath()) {
                                        return $image;
                                    }
                                    else {
                                        return null;
                                    }
                                }
                            )
                        );

        // Mock the EntityManager to return the mock of the repository
        $entityManager = $this->getMockBuilder(EntityManager::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $entityManager->expects($this->any())
                      ->method('getRepository')
                      ->will($this->returnValue($imageRepository));


        $fileToObject = new FileToObject($entityManager, 'ResponsiveImage');

        // test with non-existing filename.
        $image = $fileToObject->getObjectFromFilename('not-here.jpg');
        $this->assertEmpty($image);

        // Test with existing image.
        $image = $fileToObject->getObjectFromFilename('dummy.jpg');
        $this->assertInstanceOf(ResponsiveImageInterface::class, $image);
    }
}

<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class ImageController
 *
 * @package ResponsiveImageBundle\Controller
 */
class ImageController extends AbstractController
{

    /**
     * @param                                           $stylename
     * @param                                           $filename
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function indexAction($stylename, $filename, Request $request)
    {
        // Get image style information.
        if (empty($this->get('responsive_image.style_manager')->styleExists($stylename))) {
            throw $this->createNotFoundException('The style does not exist');
        }

        return $this->createDerivedImageResponse($filename, $stylename);
    }

    /**
     * @param string                                    $filename
     * @param string|array                              $style
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function createDerivedImageResponse(string $filename, $style)
    {
        // Create image if the file exists.
        $imageObject = $this->get('responsive_image.file_to_object')->getObjectFromFilename($filename);
        if (!empty($imageObject)) {
            $generatedImageArray = $this->get('responsive_image.image_manager')->createStyledImages($imageObject, [$style]);

            if (!empty($generatedImageArray[$style])) {
                $path = $generatedImageArray[$style];

                $cache  = $this->get('responsive_image.file_system_wrapper')->getAdapter();
                $stream = $cache->readStream($path);

                $response = new StreamedResponse();
                $response->headers->set('Content-Type', $cache->getMimetype($path));
                $response->headers->set('Content-Length', $cache->getSize($path));
                $response->setPublic();
                $response->setMaxAge(31536000); // @TODO:
                $response->setExpires(date_create()->modify('+1 years')); // @TODO:

                $response->setCallback(
                    function () use ($stream) {
                        if (ftell($stream['stream']) !== 0) {
                            rewind($stream['stream']);
                        }
                        fpassthru($stream['stream']);
                        fclose($stream['stream']);
                    }
                );

                return $response;
            }
            else {
                throw $this->createNotFoundException('Derived image could not be created');
            }
        }
        else {
            throw $this->createNotFoundException('The file does not exist');
        }
    }
}

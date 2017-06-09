<?php

namespace IrishDan\ResponsiveImageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ImageController
 *
 * @package ResponsiveImageBundle\Controller
 */
class ImageController extends Controller
{
    /**
     * Generates a derivative image as a response
     *
     * @param $stylename
     * @param $filename
     * @return BinaryFileResponse
     */
    public function indexAction($stylename, $filename)
    {
        // Get image style information.
        if (empty($this->get('responsive_image.style_manager')->styleExists($stylename))) {
            throw $this->createNotFoundException('The style does not exist');
        }

        // Create image if the file exists.
        $imageEntityClass = $this->getParameter('responsive_image.entity_class');
        $imageObject = $this->get('responsive_image.file_to_object')->getObjectFromFilename($filename, $imageEntityClass);

        if (!empty($imageObject)) {
            if (!empty($imageObject)) {
                $generatedImageArray = $this->get('responsive_image.image_manager')->createStyledImages($imageObject, [$stylename]);
            }

            if (!empty($generatedImageArray[$stylename])) {
                $response = new BinaryFileResponse($generatedImageArray[$stylename]);
            } else {
                throw $this->createNotFoundException('Derived image could not be created');
            }
        } else {
            throw $this->createNotFoundException('The file does not exist');
        }

        return $response;
    }
}

<?php

namespace ResponsiveImageBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ImageController
 * @package ResponsiveImageBundle\Controller
 */
class ImageController extends Controller
{
    /**
     * @param $stylename
     * @param $filename
     * @return BinaryFileResponse
     */
    public function indexAction($styleName, $fileName)
    {
        // Get image style information.
        if (empty($this->get('responsive_image.style_manager')->styleExists($styleName))) {
            throw $this->createNotFoundException('The style does not exist');
        }

        // Create image if the file exists.
        if ($this->get('responsive_image.file_system')->fileExists($fileName)) {
            // Get the image object.
            $imageEntityClass = $this->getParameter('image_entity_class');
            $imageObject = $this->get('responsive_image.file_to_object')->getObjectFromFilename($fileName, $imageEntityClass[0]);

            if (!empty($imageObject)) {
                $image = $this->get('responsive_image.image_manager')->createStyledImage($imageObject, $styleName);
            }

            if (!empty($image)) {
                $response = new BinaryFileResponse($image);
            }
            else {
                throw $this->createNotFoundException('Derived image could not be created');
            }
        }
        else {
            throw $this->createNotFoundException('The file does not exist');
        }

        return $response;
    }
}

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
    public function indexAction($stylename, $filename)
    {
        // Get image style information.
        $style = $this->get('responsive_image.style_manager')->getStyle($stylename);
        if (empty($style)) {
            throw $this->createNotFoundException('The style does not exist');
        }

        $system = $this->get('responsive_image.file_system');
        $originalPath = $system->uploadedFilePath($filename);
        if (file_exists($originalPath)) {
            // Get the image object.
            $imageEntityClass = $this->getParameter('image_entity_class');
            $imageObject = $this->get('responsive_image.file_to_object')->getObjectFromFilename($filename, $imageEntityClass[0]);

            if (!empty($imageObject)) {
                $image = $this->get('responsive_image.image_manager')->createStyledImage($imageObject, $stylename);
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

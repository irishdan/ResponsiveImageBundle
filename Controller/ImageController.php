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
        // Get the file system service.
        $system = $this->get('responsive_image.file_system');

        $stylePath = $system->styleDirectoryPath($stylename);
        $originalPath = $system->uploadedFilePath($filename);

        // Get all image styles.
        $style = $this->get('responsive_image.style_manager')->getStyle($stylename);

        // If the file doesn't exist, show a 404 page.
        if (empty($style)) {
            throw $this->createNotFoundException('The style does not exist');
        }

        if (file_exists($originalPath)) {
            // Get the image object.
            $imageEntityClass = $this->container->getParameter('image_entity_class');
            $imageObject = $this->get('responsive_image.file_to_object')->getObjectFromFilename($filename, $imageEntityClass[0]);

            // Get crop coordinates if any.
            $crop = empty($imageObject) ? null : $imageObject->getCropCoordinates();

            $image = $this->get('responsive_image.imager')->createImage($originalPath, $stylePath, $style, $crop);
            $response = new BinaryFileResponse($image);
        }
        else {
            throw $this->createNotFoundException('The file does not exist');
        }

        return $response;
    }
}

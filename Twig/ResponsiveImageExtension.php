<?php

namespace ResponsiveImageBundle\Twig;


use ResponsiveImageBundle\Utils\ResponsiveImageInterface;
use ResponsiveImageBundle\Utils\ResponsiveImageManager;

/**
 * Class ResponsiveImageExtension
 * @package ResponsiveImageBundle\Twig
 */
class ResponsiveImageExtension extends \Twig_Extension
{

    /**
     * @var ResponsiveImageManager
     */
    private $imageManager;


    /**
     * ResponsiveImageExtension constructor.
     * @param ResponsiveImageManager $imageManager
     */
    public function __construct(ResponsiveImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('picture_image', [$this, 'generatePictureImage'], [
                'is_safe' => ['html']]),
            new \Twig_SimpleFunction('styled_image', [$this, 'generateStyledImage'], [
                'is_safe' => ['html']]),
        );
    }

    /**
     * @return string
     */
    public function generatePictureImage(ResponsiveImageInterface $image, $pictureSet)
    {
        return $this->imageManager->setPictureSet($image, $pictureSet);
    }

    /**
     * @return string
     */
    public function generateStyledImage(ResponsiveImageInterface $image, $styleName)
    {
        return $this->imageManager->setImageStyle($image, $styleName);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'responsive_image_extension';
    }
}
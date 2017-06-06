<?php

namespace IrishDan\ResponsiveImageBundle\Twig;


use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use IrishDan\ResponsiveImageBundle\ResponsiveImageManager;
use IrishDan\ResponsiveImageBundle\StyleManager;

/**
 * Class ResponsiveImageExtension
 *
 * @package ResponsiveImageBundle\Twig
 */
class ResponsiveImageExtension extends \Twig_Extension
{
    private $styleManager;

    public function __construct(StyleManager $styleManager)
    {
        $this->styleManager = $styleManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('picture_image', [$this, 'generatePictureImage'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new \Twig_SimpleFunction('styled_image', [$this, 'generateStyledImage'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new \Twig_SimpleFunction('background_responsive_image', [$this, 'generateBackgroundImage'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
        ];
    }

    public function generateBackgroundImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $pictureSetName, $selector)
    {
        $mq = $this->styleManager->getMediaQuerySourceMappings($image, $pictureSetName);

        $original = $mq[0];
        unset($mq[0]);

        return $environment->render('ResponsiveImageBundle::css.html.twig', [
            'original' => $original,
            'mq_mappings' => $mq,
            'selector' => $selector,
            'image' => $image,
        ]);
    }

    public function generatePictureImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $pictureSetName)
    {
        $mq = $this->styleManager->getMediaQuerySourceMappings($image, $pictureSetName);

        $original = $mq[0];
        unset($mq[0]);

        return $environment->render('ResponsiveImageBundle::picture.html.twig', [
            'original' => $original,
            'mq_mappings' => $mq,
            'image' => $image,
        ]);
    }

    public function generateStyledImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $styleName)
    {
        $this->styleManager->setImageStyle($image, $styleName);

        return $environment->render('ResponsiveImageBundle::img.html.twig', [
            'image' => $image,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'responsive_image_extension';
    }
}
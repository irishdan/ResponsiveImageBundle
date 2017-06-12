<?php

namespace IrishDan\ResponsiveImageBundle\Twig;


use IrishDan\ResponsiveImageBundle\Image\ImageManager;
use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use IrishDan\ResponsiveImageBundle\StyleManager;
use IrishDan\ResponsiveImageBundle\UrlBuilder;

/**
 * Class ResponsiveImageExtension
 *
 * @package ResponsiveImageBundle\Twig
 */
class ResponsiveImageExtension extends \Twig_Extension
{
    private $styleManager;
    private $urlBuilder;
    private $imageManager;

    public function __construct(StyleManager $styleManager, UrlBuilder $urlBuilder, ImageManager $imageManager = null)
    {
        $this->styleManager = $styleManager;
        $this->urlBuilder = $urlBuilder;
        $this->imageManager = $imageManager;
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
            new \Twig_SimpleFunction('crop_image', [$this, 'cropImage'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new \Twig_SimpleFunction('scale_image', [$this, 'scaleImage'], [
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

        foreach ($mq as $index => $path) {
            $mq[$index] = $this->urlBuilder->filePublicUrl($path);
        }

        $original = $mq[0];
        unset($mq[0]);

        return $environment->render('ResponsiveImageBundle::css.html.twig', [
            'original' => $original,
            'mq_mappings' => $mq,
            'selector' => $selector,
            'image' => $image,
        ]);
    }

    public function generatePictureImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $pictureSetName, $generate = false)
    {
        // @TODO: Implement generate if missing.

        $mq = $this->styleManager->getMediaQuerySourceMappings($image, $pictureSetName);

        foreach ($mq as $index => $path) {
            $mq[$index] = $this->urlBuilder->filePublicUrl($path);
        }

        $original = $mq[0];
        unset($mq[0]);

        return $environment->render('ResponsiveImageBundle::picture.html.twig', [
            'original' => $original,
            'mq_mappings' => $mq,
            'image' => $image,
        ]);
    }

    public function generateStyledImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $styleName, $generate = false)
    {
        // @TODO: Implement generate if missing.

        $stylePath = $this->styleManager->getStylePath($image, $styleName);

        return $this->renderImage($environment, $image, $stylePath);
    }

    public function cropImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $width = 10, $height = 10)
    {
        // $this->styleManager->generate
        // @TODO: To avoid creating images, that already exist, check if it exists, need a way to disable this checking
        // @TODO: We could potentially cache a contents list
        $styleName = 'custom_crop_' . $width . '_' . $height;

        $image->setWidth($width);
        $image->setHeight($height);

        if (!empty($this->imageManager)) {
            $this->imageManager->createCustomStyledImage($image, $styleName);
        }

        // @TODO: Its not just about setting the src, its also about setting the height and width, so a method is needed
        $stylePath = $this->styleManager->getStylePath($image, $styleName);

        dump($stylePath);

        return $this->renderImage($environment, $image, $stylePath);
    }

    public function scaleImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $width = 10, $height = 10)
    {
        $styleName = 'custom_scale_w' . $width . '_h' . $height;
        $stylePath = $this->styleManager->getStylePath($image, $styleName);

        return $this->renderImage($environment, $image, $stylePath);
    }

    protected function renderImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $stylePath)
    {
        $src = $this->urlBuilder->filePublicUrl($stylePath);
        // @TODO: Add this int ge image interface.
        $image->setSrc($src);

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
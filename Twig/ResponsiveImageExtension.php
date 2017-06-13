<?php

namespace IrishDan\ResponsiveImageBundle\Twig;


use IrishDan\ResponsiveImageBundle\ImageProcessing\ImageManager;
use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use IrishDan\ResponsiveImageBundle\StyleManager;
use IrishDan\ResponsiveImageBundle\Url\UrlBuilder;

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
        $this->urlBuilder   = $urlBuilder;
        $this->imageManager = $imageManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'picture_image', [$this, 'generatePictureImage'], [
                    'is_safe'           => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new \Twig_SimpleFunction(
                'styled_image', [$this, 'generateStyledImage'], [
                    'is_safe'           => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new \Twig_SimpleFunction(
                'crop_image', [$this, 'cropImage'], [
                    'is_safe'           => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new \Twig_SimpleFunction(
                'scale_image', [$this, 'scaleImage'], [
                    'is_safe'           => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new \Twig_SimpleFunction(
                'background_responsive_image', [$this, 'generateBackgroundImage'], [
                    'is_safe'           => ['html'],
                    'needs_environment' => true,
                ]
            ),
        ];
    }

    public function generateBackgroundImage(
        \Twig_Environment $environment,
        ResponsiveImageInterface $image,
        $pictureSetName,
        $selector
    ) {
        $mq = $this->styleManager->getMediaQuerySourceMappings($image, $pictureSetName);

        foreach ($mq as $index => $path) {
            $mq[$index] = $this->urlBuilder->filePublicUrl($path);
        }

        $original = $mq[0];
        unset($mq[0]);

        return $environment->render(
            'ResponsiveImageBundle::css.html.twig',
            [
                'original'    => $original,
                'mq_mappings' => $mq,
                'selector'    => $selector,
                'image'       => $image,
            ]
        );
    }

    public function generatePictureImage(
        \Twig_Environment $environment,
        ResponsiveImageInterface $image,
        $pictureSetName,
        $generate = false
    ) {
        // @TODO: Implement generate if missing.

        $mq = $this->styleManager->getMediaQuerySourceMappings($image, $pictureSetName);

        foreach ($mq as $index => $path) {
            $mq[$index] = $this->urlBuilder->filePublicUrl($path);
        }

        $original = $mq[0];
        unset($mq[0]);

        return $environment->render(
            'ResponsiveImageBundle::picture.html.twig',
            [
                'original'    => $original,
                'mq_mappings' => $mq,
                'image'       => $image,
            ]
        );
    }

    public function generateStyledImage(
        \Twig_Environment $environment,
        ResponsiveImageInterface $image,
        $styleName = null,
        $generate = false
    ) {
        // @TODO: Implement generate if missing.

        return $this->renderImage($environment, $image, $styleName);
    }

    public function cropImage(
        \Twig_Environment $environment,
        ResponsiveImageInterface $image,
        $width = '',
        $height = null
    ) {
        $styleName = 'custom_crop_' . $width . '_' . $height;

        if (!empty($this->imageManager)) {
            $this->imageManager->createCustomStyledImage($image, $styleName);
        }

        return $this->renderImage($environment, $image, $styleName);
    }

    public function scaleImage(
        \Twig_Environment $environment,
        ResponsiveImageInterface $image,
        $width = '',
        $height = ''
    ) {
        $styleName = 'custom_scale_' . $width . '_' . $height;

        if (!empty($this->imageManager)) {
            $this->imageManager->createCustomStyledImage($image, $styleName);
        }

        return $this->renderImage($environment, $image, $styleName);
    }

    protected function renderImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $styleName = null)
    {
        if (!empty($styleName)) {
            $path = $this->styleManager->getStylePath($image, $styleName);
        }
        else {
            $path = $image->getPath();
        }

        dump($path);
        $src = $this->urlBuilder->filePublicUrl($path);
        dump($src);
        // Set the image attributes.
        $this->styleManager->setImageAttributes($image, $styleName, $src);

        return $environment->render(
            'ResponsiveImageBundle::img.html.twig',
            [
                'image' => $image,
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'responsive_image_extension';
    }
}
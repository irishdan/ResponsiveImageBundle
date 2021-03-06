<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

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
    /**
     * @var StyleManager
     */
    private $styleManager;
    /**
     * @var UrlBuilder
     */
    private $urlBuilder;
    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * ResponsiveImageExtension constructor.
     *
     * @param StyleManager      $styleManager
     * @param UrlBuilder        $urlBuilder
     * @param ImageManager|null $imageManager
     */
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
                'sizes_image', [$this, 'generateSizesImage'], [
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

    /**
     * @param \Twig_Environment        $environment
     * @param ResponsiveImageInterface $image
     * @param                          $pictureSetName
     * @param                          $selector
     *
     * @return mixed|string
     */
    public function generateBackgroundImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $pictureSetName, $selector)
    {
        $cssData = $this->styleManager->getPictureData($image, $pictureSetName);
        $this->convertPathsToUrls($cssData, ['fallback', 'sources']);

        return $environment->render(
            'ResponsiveImageBundle::css.html.twig',
            [
                'fallback' => $cssData['fallback'],
                'sources'  => $cssData['sources'],
                'image'    => $image,
                'selector' => $selector,
            ]
        );
    }

    /**
     * @param \Twig_Environment        $environment
     * @param ResponsiveImageInterface $image
     * @param                          $pictureSetName
     * @param bool                     $generate
     *
     * @return mixed|string
     */
    public function generatePictureImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $pictureSetName, $generate = false)
    {
        $pictureData = $this->styleManager->getPictureData($image, $pictureSetName);
        $this->convertPathsToUrls($pictureData, ['fallback', 'sources']);

        return $environment->render(
            'ResponsiveImageBundle::picture.html.twig',
            [
                'fallback' => $pictureData['fallback'],
                'sources'  => $pictureData['sources'],
                'image'    => $image,
            ]
        );
    }

    /**
     * @internal
     *
     * @param array $data
     * @param array $keys
     */
    private function convertPathsToUrls(array &$data, array $keys)
    {
        foreach ($keys as $key) {
            if (is_array($data[$key])) {
                $subData = [];
                foreach ($data[$key] as $item => $path) {
                    $subData[$item] = $this->createPublicFileUrl($path);
                }
                $data[$key] = $subData;
            }
            else {
                $data[$key] = $this->createPublicFileUrl($data[$key]);
            }
        }
    }

    /**
     * Some path strings could contain more than one path, eg 1x and 2x paths.
     * This function breaks them up and converts to the full public url.
     *
     * @param $path
     *
     * @return string
     */
    private function createPublicFileUrl($path)
    {
        $pathArray = explode(',', $path);
        foreach ($pathArray as $key => $item) {
            $pathArray[$key] = $this->urlBuilder->filePublicUrl(trim($item));
        }

        return implode(', ', $pathArray);
    }

    /**
     * @param \Twig_Environment        $environment
     * @param ResponsiveImageInterface $image
     * @param                          $pictureSetName
     * @param bool                     $generate
     *
     * @return mixed|string
     */
    public function generateSizesImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $pictureSetName, $generate = false)
    {
        $sizesData = $this->styleManager->getImageSizesData($image, $pictureSetName);

        // Replace the relative path with the real path.
        foreach ($sizesData['srcsets'] as $path => $width) {
            $sizesData['srcsets'][$path] = $this->urlBuilder->filePublicUrl($path) . ' ' . $width . 'w';
        }

        // @TODO: We need to incorporate a fallback image style
        return $environment->render(
            'ResponsiveImageBundle::img_sizes.html.twig',
            [
                'image'   => $image,
                'sizes'   => implode(', ', $sizesData['sizes']),
                'srcsets' => implode(', ', $sizesData['srcsets']),
            ]
        );
    }

    /**
     * @param \Twig_Environment        $environment
     * @param ResponsiveImageInterface $image
     * @param null                     $styleName
     * @param bool                     $generate
     *
     * @return mixed|string
     */
    public function generateStyledImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $styleName = null, $generate = false)
    {
        // @TODO: Implement generate if missing.

        return $this->renderImage($environment, $image, $styleName);
    }

    /**
     * @param \Twig_Environment        $environment
     * @param ResponsiveImageInterface $image
     * @param string                   $width
     * @param null                     $height
     *
     * @return mixed|string
     */
    public function cropImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $width = '', $height = null)
    {
        $styleName = 'custom_crop_' . $width . '_' . $height;

        if (!empty($this->imageManager)) {
            $this->imageManager->createCustomStyledImage($image, $styleName);
        }

        return $this->renderImage($environment, $image, $styleName);
    }

    /**
     * @param \Twig_Environment        $environment
     * @param ResponsiveImageInterface $image
     * @param string                   $width
     * @param string                   $height
     *
     * @return mixed|string
     */
    public function scaleImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $width = '', $height = '')
    {
        $styleName = 'custom_scale_' . $width . '_' . $height;

        if (!empty($this->imageManager)) {
            $this->imageManager->createCustomStyledImage($image, $styleName);
        }

        return $this->renderImage($environment, $image, $styleName);
    }

    /**
     * @param \Twig_Environment        $environment
     * @param ResponsiveImageInterface $image
     * @param null                     $styleName
     *
     * @return mixed|string
     */
    protected function renderImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $styleName = null)
    {
        if (!empty($styleName)) {
            $path = $this->styleManager->getStylePath($image, $styleName);
        }
        else {
            $path = $image->getPath();
        }

        $src = $this->urlBuilder->filePublicUrl($path);
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
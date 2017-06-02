<?php

namespace IrishDan\ResponsiveImageBundle\Event;

/**
 * Class ImageEvents
 *
 * @package ResponsiveImageBundle\Event
 */
final class ImageEvents {
    /**
     * Generate styled images.
     */
    const IMAGE_GENERATE_STYLED = 'responsive_image.image_generate_styled';

    /**
     * Delete original and styled images for a given image object.
     */
    const IMAGE_DELETE_ALL = 'responsive_image.image_delete_all';

    /**
     * Delete original and styled images for a given image object.
     */
    const IMAGE_DELETE_ORIGINAL = 'responsive_image.image_delete_original';

    /**
     * Delete styled images for a given image object.
     */
    const IMAGE_DELETE_STYLED = 'responsive_image.image_delete_styled';

    /**
     * Delete all images of belonging to a style
     */
    const STYLE_DELETE_STYLED = 'responsive_image.style_delete_styled';

    /**
     * Delete all styled images
     */
    const STYLE_DELETE_ALL = 'responsive_image.style_delete_all';
}
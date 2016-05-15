<?php

namespace ResponsiveImageBundle\Event;

/**
 * Class ImageEvents
 *
 * @package ResponsiveImageBundle\Event
 */
final class ImageEvents {
    /**
     * Generate styled images.
     */
    const TRIGGER_IMAGE_GENERATE_STYLED = 'responsive_image.image_generate_styled';

    /**
     * Delete original and styled images for a given image object.
     */
    const TRIGGER_IMAGE_DELETE_ALL = 'responsive_image.image_delete_all';

    /**
     * Delete styled images for a given image object.
     */
    const TRIGGER_IMAGE_DELETE_STYLED = 'responsive_image.image_delete_styled';

    /**
     * Delete all images of belonging to a style
     */
    const TRIGGER_STYLE_DELETE_STYLED = 'responsive_image.style_delete_styled';

    /**
     * Delete all styled images
     */
    const TRIGGER_STYLE_DELETE_ALL = 'responsive_image.style_delete_all';
}
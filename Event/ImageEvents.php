<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Event;

/**
 * Class ImageEvents
 *
 * @package IrishDan\ResponsiveImageBundle\Event
 */
final class ImageEvents
{
    const IMAGE_CREATED = 'responsive_image.image_created';
    const IMAGE_UPDATED = 'responsive_image.image_updated';
    const IMAGE_DELETED = 'responsive_image.image_deleted';
}
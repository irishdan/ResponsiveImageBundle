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

use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class StyledImagesEvent
 *
 * @package IrishDan\ResponsiveImageBundle\Event
 */
class StyledImagesEvent extends Event
{
    /**
     * @var ResponsiveImageInterface
     */
    protected $image;
    /**
     * @var array
     */
    protected $styleImageLocationArray = [];

    /**
     * StyledImagesEvent constructor.
     *
     * @param ResponsiveImageInterface|null $image
     * @param array                         $styleImageLocationArray
     */
    public function __construct(ResponsiveImageInterface $image = null, array $styleImageLocationArray = [])
    {
        $this->image                   = $image;
        $this->styleImageLocationArray = $styleImageLocationArray;
    }

    /**
     * @return array
     */
    public function getStyleImageLocationArray()
    {
        return $this->styleImageLocationArray;
    }

    /**
     * @return ResponsiveImageInterface|null
     */
    public function getImage()
    {
        return $this->image;
    }
}
<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Url;


/**
 * Class LocalAdapterUrlEncoder
 *
 * @package IrishDan\ResponsiveImageBundle\Url
 */
class LocalAdapterUrlEncoder implements UrlEncoderInterface
{
    /**
     * @param            $adapter
     * @param array|null $config
     *
     * @return mixed|string
     */
    public function getUrl($adapter, array $config = null)
    {
        $path = empty($config['image_directory']) ? 'image' : $config['image_directory'];

        return $path;
    }

    public function getData($adapter, array $config = null)
    {
        return [
            empty($config['image_directory']) ? 'image' : $config['image_directory'],
        ];
    }
}
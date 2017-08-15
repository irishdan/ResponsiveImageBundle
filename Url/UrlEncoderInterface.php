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
 * Interface UrlEncoderInterface
 *
 * @package IrishDan\ResponsiveImageBundle\Url
 */
interface UrlEncoderInterface
{
    /**
     * @param            $adapter
     * @param array|null $config
     *
     * @return mixed
     */
    public function getUrl($adapter, array $config = null);

    /**
     * @param            $data
     * @param array|null $config
     *
     * @return mixed
     */
    public function getData($data, array $config = null);
}
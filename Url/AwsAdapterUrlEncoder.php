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
 * Class AwsAdapterUrlEncoder
 *
 * @package IrishDan\ResponsiveImageBundle\Url
 */
class AwsAdapterUrlEncoder implements UrlEncoderInterface
{
    /**
     * @param            $adapter
     * @param array|null $config
     *
     * @return mixed|string
     */
    public function getUrlFromAdapter($adapter, array $config = null)
    {
        $data = [];

        $data['prefix'] = $adapter->getPathPrefix();
        $data['bucket'] = $adapter->getBucket();
        $data['region'] = $adapter->getClient()->getRegion();

        return $this->getUrlFromData($data);
    }

    /**
     * @param            $data
     * @param array|null $config
     *
     * @return string
     */
    public function getUrlFromData($data, array $config = null)
    {
        // @TODO: Get http from the service definition if possible.

        return 'https://' . 's3-' . $data['region'] . '.amazonaws.com/' . $data['bucket'] . '/' . $data['prefix'] . '/';
    }
}
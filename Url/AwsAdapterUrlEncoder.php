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
    public function getUrl($adapter, array $config = null)
    {
        $data = $this->getDataArray($adapter);

        return 'https://' . 's3-' . $data['region'] . '.amazonaws.com/' . $data['bucket'] . '/' . $data['prefix'] . '/';
    }

    /**
     * @param            $adapter
     * @param array|null $config
     *
     * @return string
     * @internal param $data
     */
    public function getData($adapter, array $config = null)
    {
        return $this->getDataArray($adapter);
    }

    private function getDataArray($adapter)
    {
        $data = [];

        $data['prefix'] = $adapter->getPathPrefix();
        $data['bucket'] = $adapter->getBucket();
        $data['region'] = $adapter->getClient()->getRegion();

        return $data;
    }
}
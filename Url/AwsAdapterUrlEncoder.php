<?php

namespace IrishDan\ResponsiveImageBundle\Url;

class AwsAdapterUrlEncoder implements UrlEncoderInterface
{
    public function getUrlFromAdapter($adapter, array $config = null)
    {
        $data = [];

        $data['prefix'] = $adapter->getPathPrefix();
        $data['bucket'] = $adapter->getBucket();
        $data['region'] = $adapter->getClient()->getRegion();

        return $this->getUrlFromData($data);
    }

    public function getUrlFromData($data, array $config = null)
    {
        //

        return 'https://' . 's3-' . $data['region'] . '.amazonaws.com/' . $data['bucket'] . '/' . $data['prefix'] . '/';
    }
}
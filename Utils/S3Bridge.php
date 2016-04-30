<?php

namespace ResponsiveImageBundle\Utils;


use Aws\CommandPool;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;

/**
 * Class S3Bridge
 * @package ResponsiveImageBundle\Utils
 */
class S3Bridge
{
    /**
     * @var
     */
    private $bucket;

    /**
     * @var
     */
    private $region;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var
     */
    private $key;

    /**
     * @var
     *
     * ['system/location', 'styled/location']
     */
    private $paths;

    /**
     * @var
     */
    private $secret;

    /**
     * @var
     */
    private $s3;

    /**
     * S3Bridge constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->bucket = $config['bucket'];
        $this->directory = empty($config['directory']) ? '' : $config['directory'] . '/';
        $this->key = $config['access_key_id'];
        $this->region = $config['region'];
        $this->secret = $config['secret_access_key'];
        $this->version = $config['version'];
    }

    /**
     *
     */
    public function getClient() {
        // AWS access info
        $this->s3 = S3Client::factory([
            'version' => $this->version,
            'region'  => $this->region,
            'credentials' => [
                'key'     => $this->key,
                'secret'  => $this->secret,
            ]
        ]);
    }

    public function setPaths($paths, $clear = FALSE) {
        if ($clear) {
            $this->paths = [];
        }
        foreach ($paths as $systemLocation => $styledLocation) {
            $this->paths[$systemLocation] = $styledLocation;
        }
    }

    /**
     *
     */
    public function uploadToS3()
    {
        $this->getClient();
        $commands = array();

        foreach ($this->paths as $path => $file) {
            $commands[] = $this->s3->getCommand(
                'PutObject', array(
                    'region'       => $this->region,
                    'Bucket'       => $this->bucket,
                    'Key'          => $this->directory . $file,
                    'SourceFile'   => $path,
                    'ACL'          => 'public-read',
                    'StorageClass' => 'REDUCED_REDUNDANCY',
                )
            );

            $pool = new CommandPool($this->s3, $commands);
            $promise = $pool->promise();

            // Force the pool to complete synchronously
            try {
                $result = $promise->wait();
            } catch (AwsException $e) {
                // handle the error.
            }
        }
    }

    /**
     *
     */
    public function removeFroms3()
    {
        $this->getClient();
    }
}
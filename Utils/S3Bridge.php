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
     * Initialise the S3 client.
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

    /**
     * Removes all the images set in the $this->paths array from the configured S3 bucket.
     */
    public function removeFromS3()
    {
        $this->getClient();

        $objects = [];
        foreach ($this->paths as $path => $file) {
            $objects[] = ['Key' => $this->directory . $file];
        }
        $result = $this->s3->deleteObjects(array(
            'Bucket'  => $this->bucket,
            'Delete' => [
                'Objects' => $objects,
            ],
        ));
    }

    /**
     *  Sync Bucket with local entities.
     */
    public function syncBucket() {

    }

    /**
     * @param $paths
     * @param bool $clear
     */
    public function setPaths($paths, $clear = FALSE) {
        if ($clear) {
            $this->paths = [];
        }
        foreach ($paths as $systemLocation => $styledLocation) {
            $this->paths[$systemLocation] = $styledLocation;
        }
    }

    /**
     * Tranfers all the images set in the $this->paths array to the configured S3 bucket.
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
}
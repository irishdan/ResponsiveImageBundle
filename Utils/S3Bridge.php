<?php

namespace ResponsiveImageBundle\Utils;


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
        foreach ($this->paths as $path => $file) {
            try{
                // Upload a file.
                $result = $this->s3->putObject(array(
                    'region'       => $this->region,
                    'Bucket'       => $this->bucket,
                    'Key'          => $this->directory . $file,
                    'SourceFile'   => $path,
                    'ACL'          => 'public-read',
                    'StorageClass' => 'REDUCED_REDUNDANCY',
                ));
                // var_dump($result);
            } catch (\Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }

        // $s3 = new S3Client(awsAccessKey, awsSecretKey);
        // $s3->putBucket($bucket, \S3::ACL_PUBLIC_READ);
//
        // $commands = array();
        // $commands[] = $s3->getCommand('PutObject', array(
        //     'Bucket' => 'SOME_BUCKET',
        //     'Key' => 'photos/photo01.jpg',
        //     'Body' => fopen('/tmp/photo01.jpg', 'r'),
        // ));
        // $commands[] = $s3->getCommand('PutObject', array(
        //     'Bucket' => 'SOME_BUCKET',
        //     'Key' => 'photos/photo02.jpg',
        //     'Body' => fopen('/tmp/photo02.jpg', 'r'),
        // ));
//
        // // Execute an array of command objects to do them in parallel
        // $s3->execute($commands);
//
        // // Loop over the commands, which have now all been executed
        // foreach ($commands as $command) {
        //     $result = $command->getResult();
        //     // Do something with result
        // }
    }

    /**
     *
     */
    public function removeFroms3()
    {
        $this->getClient();
    }
}
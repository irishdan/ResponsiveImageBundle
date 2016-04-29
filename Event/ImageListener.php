<?php

namespace ResponsiveImageBundle\Event;

use ResponsiveImageBundle\Event\ImageEvent;

/**
 * Class ImageListener
 * @package ResponsiveImageBundle\Event
 */
class ImageListener {
    /**
     * @var
     */
    private $config;

    /**
     * ImageListener constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param \ResponsiveImageBundle\Event\ImageEvent $event
     */
    public function onImageGenerated(ImageEvent $event)
    {
        if (!empty($this->config['aws_s3'])) {
            // $awsConfig = $this->config['aws_s3'];
            // From here can upload to S3.
            // aws_s3:
            //     enabled: TRUE
            //     key: AWS_S3_KEY
            //     secret: AWS_S3_SECRET
            //     region: AWS_S3_REGION
        }

    }

    /**
     * @param \ResponsiveImageBundle\Event\ImageEvent $event
     */
    public function onImageCreated(ImageEvent $event) {

    }

    /**
     * @param \ResponsiveImageBundle\Event\ImageEvent $event
     */
    public function onImageUpdated(ImageEvent $event) {
        
    }
}
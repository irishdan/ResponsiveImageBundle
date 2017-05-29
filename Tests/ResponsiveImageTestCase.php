<?php

namespace ResponsiveImageBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ResponsiveImageTestCase extends WebTestCase
{
    protected $parameters = [
        'debug' => false,
        'image_compression' => 90,
        'image_directory' => 'uploads/documents',
        'image_driver' => 'gd',
        'image_styles_directory' => 'styles',
        'breakpoints' => [
            'base' => 'min-width: 0px',
            'desktop' => 'min-width: 1100px',
            'tv' => 'min-width: 1800px',
        ],
        'image_styles' => [
            'thumb' => [
                'effect' => 'crop',
                'width' => 180,
                'height' => 180,
            ],
        ],
        'picture_sets' => [
            'thumb_picture' => [
                'base' => [
                    'effect' => 'crop',
                    'width' => 300,
                    'height' => 600,
                ],
                'desktop' => 'thumb',
            ],
        ],
    ];
}
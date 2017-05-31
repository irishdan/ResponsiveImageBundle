<?php

namespace ResponsiveImageBundle\Test\Controller;

use ResponsiveImageBundle\Tests\ResponsiveImageTestCase;

class ImageControllerTest extends ResponsiveImageTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = $this->getService('test.client');
    }

    public function testItRunsSuccessfully()
    {
        // @TODO: Sort it out.
        $response = $this->client->request(
            'GET',
            '/test/images/styles/thumb/dummy.jpg',
            [],
            []
        );
        // $this->assertTrue($response->isSuccessful());
    }
}
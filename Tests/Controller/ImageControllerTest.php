<?php

namespace ResponsiveImageBundle\Test\Controller;

use ResponsiveImageBundle\Tests\ResponsiveImageTestCase;

class ImageControllerTest extends ResponsiveImageTestCase
{
    private $client;

    protected function setUp()
    {
        // Create the db
        $created = $this->runCommand('doctrine:database:create');
        // var_dump(' created: ' . $created);

        // Create db tables
        $schema = $this->runCommand('doctrine:schema:create');
        // var_dump('schema: ' . $schema);

        // Create the client
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
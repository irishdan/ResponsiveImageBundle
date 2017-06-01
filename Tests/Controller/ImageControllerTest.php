<?php

namespace ResponsiveImageBundle\Test\Controller;

use ResponsiveImageBundle\Tests\Entity\TestImage;
use ResponsiveImageBundle\Tests\ResponsiveImageTestCase;

class ImageControllerTest extends ResponsiveImageTestCase
{
    private $client;
    private $em;

    protected function setUp()
    {
        // Create the db
        $created = $this->runCommand('doctrine:database:create --if-not-exists');

        // Create db tables
        $schema = $this->runCommand('doctrine:schema:update --force');

        $image = new TestImage();
        $this->em = $this->getService('doctrine.orm.entity_manager');

        $this->em->persist($image);
        $this->em->flush();

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
<?php

namespace ResponsiveImageBundle\Test\Controller;

use ResponsiveImageBundle\Tests\ResponsiveImageTestCase;

class ImageControllerTest extends ResponsiveImageTestCase
{
    protected function setUp()
    {
        parent::setUp();

        // Create the image in database
        // move file to correct location
    }

    protected function teardown()
    {
        parent::tearDown();

        // Remove the image from database
        // removes files
    }

    public function testStyledImageIsGenerated()
    {
        // $data = [
        //     'title' => 'Article title',
        //     'body' => 'Article body',
        // ];
//
        // // Create an article resource
        // $token = $this->getJWTToken('nomad_user');
        // $response = $this->client->post(
        //     '/api/articles',
        //     [
        //         'body' => json_encode($data),
        //         'headers' => [
        //             'Authorization' => 'Bearer ' . $token,
        //         ],
        //     ]);
//
        // $this->assertEquals(201, $response->getStatusCode());
        // $this->assertTrue($response->hasHeader('Location'));
//
        // $finishedData = json_decode($response->getBody(true), true);
//
        // $this->assertArrayHasKey('title', $finishedData);
        // $this->assertEquals('Article title', $finishedData['title']);
    }
}
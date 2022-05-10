<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RouteTest extends WebTestCase
{
    public function testPageIsSuccessful()
    {
        $client = self::createClient();
        $urls = ['/home', '/add', '/chart'];
        
        foreach ($urls as $url) {
            $client->getRequest('GET', $url);
            $this->assertSame(200, $client->getResponse()->getStatusCode());
            echo $client->getResponse()->getContent();
        }
    }
}
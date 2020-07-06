<?php declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GraphControllerTest extends WebTestCase
{
    public function testShowPost(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('#canvas')->count());
    }
}

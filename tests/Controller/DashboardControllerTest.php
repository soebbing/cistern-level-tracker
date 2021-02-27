<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    public function testShowPost(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals(1, $crawler->filter('#canvas')->count());
    }
}

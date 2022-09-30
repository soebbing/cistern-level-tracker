<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 * @coversNothing
 */
final class DashboardControllerTest extends WebTestCase
{
    public function testShowPost(): void
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/admin');

        static::assertSame(200, $client->getResponse()->getStatusCode());
        static::assertSame(1, $crawler->filter('#canvas')->count());
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\LevelFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 * @coversNothing
 */
final class StoreControllerTest extends WebTestCase
{
    public function testDateValueIsStored(): void
    {
        $client = self::createClient();

        $client->request('GET', '/add/1.11/2020-02-20%2020:02:20');

        self::assertSame(200, $client->getResponse()->getStatusCode());
        $json = json_decode($client->getResponse()->getContent(), true);

        self::assertSame(1.11, $json['liter']);
        self::assertSame(
            (new \DateTimeImmutable($json['datetime']['date']))->format('Y-m-d H:i:s'),
            (new \DateTimeImmutable('2020-02-20 20:02:20'))->format('Y-m-d H:i:s'),
        );
    }

    public function testGetPositiveValueIsStored(): void
    {
        $client = self::createClient();

        $dbTools = self::getContainer()->get(DatabaseToolCollection::class);
        $dbTools->get()->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/add/1.11');
        self::assertSame(200, $client->getResponse()->getStatusCode());
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertSame(1.11, $json['liter']);
        self::assertSame(
            (new \DateTimeImmutable($json['datetime']['date']))->format('Y-m-d H:i:s'),
            (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        );
    }

    public function testGetNegativeValueThrowsError(): void
    {
        $client = self::createClient();

        $dbTools = self::getContainer()->get(DatabaseToolCollection::class);
        $dbTools->get()->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/add/-1.0');
        self::assertSame(500, $client->getResponse()->getStatusCode());
    }

    public function testPostEmptyValueThrowsError(): void
    {
        $client = self::createClient();

        $dbTools = self::getContainer()->get(DatabaseToolCollection::class);
        $dbTools->get()->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('POST', '/add');
        self::assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testGetEmptyValueThrowsError(): void
    {
        $client = self::createClient();

        $dbTools = self::getContainer()->get(DatabaseToolCollection::class);
        $dbTools->get()->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/add');
        self::assertSame(404, $client->getResponse()->getStatusCode());
    }
}

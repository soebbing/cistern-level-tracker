<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\LevelFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StoreControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testDateValueIsStored(): void
    {
        $client = static::createClient();

        $this->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/add/1.11/2020-02-20%2020:02:20');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json = \json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(1.11, $json['liter']);
        $this->assertEquals(
            (new \DateTimeImmutable($json['datetime']['date']))->format('Y-m-d H:i:s'),
            (new \DateTimeImmutable('2020-02-20 20:02:20'))->format('Y-m-d H:i:s'),
        );
    }

    public function testGetPositiveValueIsStored(): void
    {
        $client = static::createClient();

        $this->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/add/1.11');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json = \json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(1.11, $json['liter']);
        $this->assertEquals(
            (new \DateTimeImmutable($json['datetime']['date']))->format('Y-m-d H:i:s'),
            (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        );
    }

    public function testGetNegativeValueThrowsError(): void
    {
        $client = static::createClient();

        $this->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/add/-1.0');
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    public function testPostEmptyValueThrowsError(): void
    {
        $client = static::createClient();

        $this->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('POST', '/add');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetEmptyValueThrowsError(): void
    {
        $client = static::createClient();

        $this->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/add');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}

<?php declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExportControllerTest extends WebTestCase
{
    public function testShowPost(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        \var_dump($client->getResponse()->getContent());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

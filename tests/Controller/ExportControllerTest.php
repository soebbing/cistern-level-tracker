<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\LevelFixtures;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @IgnoreAnnotation("dataProvider")
 */
class ExportControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testCorrectResponseType(): void
    {
        $client = static::createClient();

        $this->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/export');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('UTF-8', $client->getResponse()->getCharset());
        $this->assertEquals('text/csv; charset=UTF-8', $client->getResponse()->headers->get('content-type'));
        $this->assertEquals('noindex', $client->getResponse()->headers->get('x-robots-tag'));
        $this->assertEquals('attachment; filename=cistern-level-data.csv', $client->getResponse()->headers->get('content-disposition'));
    }

    /**
     * @dataProvider delimiterProvider
     */
    public function testCorrectResultNumber(string $delimiter)
    {
        $client = static::createClient();

        $this->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/export', ['delimiter' => $delimiter]);

        $rows = \str_getcsv(\trim($client->getResponse()->getContent()), "\n");
        $this->assertCount(5, $rows);

        \array_map(
            fn (string $row) => $this->assertCount(3, \str_getcsv($row, $delimiter)),
            $rows
        );
    }

    public function delimiterProvider(): array
    {
        return [
            ["\t"],
            [','],
            [';'],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\LevelFixtures;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @IgnoreAnnotation("dataProvider")
 *
 * @internal
 * @coversNothing
 */
final class ExportControllerTest extends WebTestCase
{
    public function testCorrectResponseType(): void
    {
        $client = self::createClient();

        $dbTools = self::getContainer()->get(DatabaseToolCollection::class);
        $dbTools->get()->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/export');

        static::assertSame(200, $client->getResponse()->getStatusCode());
        static::assertSame('UTF-8', $client->getResponse()->getCharset());
        static::assertSame('text/csv; charset=UTF-8', $client->getResponse()->headers->get('content-type'));
        static::assertSame('noindex', $client->getResponse()->headers->get('x-robots-tag'));
        static::assertSame('attachment; filename=cistern-level-data.csv', $client->getResponse()->headers->get('content-disposition'));
    }

    /**
     * @dataProvider delimiterProvider
     */
    public function testCorrectResultNumber(string $delimiter): void
    {
        $client = self::createClient();

        $dbTools = self::getContainer()->get(DatabaseToolCollection::class);
        $dbTools->get()->loadFixtures([
            LevelFixtures::class,
        ]);

        $client->request('GET', '/export', ['delimiter' => $delimiter]);

        $rows = str_getcsv(trim($client->getResponse()->getContent()), "\n");
        static::assertCount(5, $rows);

        array_map(
            static fn (string $row) => static::assertCount(3, str_getcsv($row, $delimiter)),
            $rows
        );
    }

    /**
     * @return array<string[]>
     */
    public function delimiterProvider(): array
    {
        return [
            ["\t"],
            [','],
            [';'],
        ];
    }
}

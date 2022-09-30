<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\DataFixtures\LevelFixtures;
use App\Entity\Level;
use App\Repository\LevelRepository;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @IgnoreAnnotation("dataProvider")
 *
 * @internal
 * @coversNothing
 */
final class LevelRepositoryTest extends WebTestCase
{
    protected function setUp(): void
    {
        $dbTools = self::getContainer()->get(DatabaseToolCollection::class);
        $dbTools->get()->loadFixtures([
            LevelFixtures::class,
        ]);

        self::bootKernel();
    }

    public function testValuesAreStored(): void
    {
        /** @var LevelRepository $repository */
        $repository = self::getContainer()->get(LevelRepository::class);

        $date = new \DateTimeImmutable('now');

        $repository->addEntry(4., $date);
        $results = $repository->getDataSince($date);

        static::assertCount(1, $results);

        /** @var Level $result */
        $result = array_shift($results);
        static::assertInstanceOf(Level::class, $result);
        static::assertSame(4., $result->getLiter());
        static::assertSame($date->getTimestamp(), $result->getDatetime()->getTimestamp());
    }

    public function testDataSinceWithoutParameterFilters(): void
    {
        /** @var LevelRepository $repository */
        $repository = self::getContainer()->get(LevelRepository::class);

        // Without defining a date, we get the results of last month
        $results = $repository->getDataSince();

        static::assertCount(2, $results);
    }

    public function testDataSinceWithDateParameterWorks(): void
    {
        /** @var LevelRepository $repository */
        $repository = self::getContainer()->get(LevelRepository::class);

        $lastMonth = (new \DateTimeImmutable('now'))->sub(new \DateInterval('P20Y'));
        $results = $repository->getDataSince($lastMonth);
        static::assertCount(4, $results);
    }

    public function testNegativeLitersThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var LevelRepository $repository */
        $repository = self::getContainer()->get(LevelRepository::class);

        $repository->addEntry(-4, new \DateTimeImmutable('now'));
    }

    public function testGetExportResultWorks(): void
    {
        /** @var LevelRepository $repository */
        $repository = self::getContainer()->get(LevelRepository::class);
        $export = $repository->getAllResults();

        static::assertContainsOnlyInstancesOf(Level::class, $export);
    }

    public function testEntity(): void
    {
        /** @var LevelRepository $repository */
        $repository = self::getContainer()->get(LevelRepository::class);

        $export = $repository->getAllResults();

        /** @var Level $entity */
        $entity = array_shift($export);
        static::assertIsInt($entity->getId());
        static::assertIsFloat($entity->getLiter());
        static::assertInstanceOf(\DateTime::class, $entity->getDateTime());

        $json = $entity->jsonSerialize();
        static::assertIsArray($json);
        static::assertArrayNotHasKey('id', $json);
        static::assertArrayHasKey('liter', $json);
        static::assertArrayHasKey('datetime', $json);
    }
}

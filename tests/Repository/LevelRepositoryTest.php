<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\DataFixtures\LevelFixtures;
use App\Entity\Level;
use App\Repository\LevelRepository;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @IgnoreAnnotation("dataProvider")
 *
 * @internal
 * @coversNothing
 */
final class LevelRepositoryTest extends WebTestCase
{
    use FixturesTrait;

    protected function setUp(): void
    {
        $this->loadFixtures([
            LevelFixtures::class,
        ]);

        self::bootKernel();
    }

    public function testValuesAreStored(): void
    {
        /** @var LevelRepository $repository */
        $repository = $this->getContainer()->get(LevelRepository::class);

        $date = new \DateTimeImmutable('now');

        $repository->addEntry(4, $date);
        $results = $repository->getDataSince($date);

        self::assertCount(1, $results);

        /** @var Level $result */
        $result = array_shift($results);
        self::assertInstanceOf(Level::class, $result);
        self::assertSame(4, $result->getLiter());
        self::assertSame($date->getTimestamp(), $result->getDatetime()->getTimestamp());
    }

    public function testDataSinceWithoutParameterFilters(): void
    {
        /** @var LevelRepository $repository */
        $repository = $this->getContainer()->get(LevelRepository::class);

        // Without defining a date, we get the results of last month
        $results = $repository->getDataSince();

        self::assertCount(2, $results);
    }

    public function testDataSinceWithDateParameterWorks(): void
    {
        /** @var LevelRepository $repository */
        $repository = $this->getContainer()->get(LevelRepository::class);

        $lastMonth = (new \DateTimeImmutable('now'))->sub(new \DateInterval('P20Y'));
        $results = $repository->getDataSince($lastMonth);
        self::assertCount(4, $results);
    }

    public function testNegativeLitersThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var LevelRepository $repository */
        $repository = $this->getContainer()->get(LevelRepository::class);

        $repository->addEntry(-4, new \DateTimeImmutable('now'));
    }

    public function testGetExportResultWorks(): void
    {
        /** @var LevelRepository $repository */
        $repository = $this->getContainer()->get(LevelRepository::class);
        $export = $repository->getAllResults();

        self::assertContainsOnlyInstancesOf(Level::class, $export);
    }

    public function testEntity(): void
    {
        /** @var LevelRepository $repository */
        $repository = $this->getContainer()->get(LevelRepository::class);

        $export = $repository->getAllResults();

        /** @var Level $entity */
        $entity = array_shift($export);
        self::assertIsInt($entity->getId());
        self::assertIsFloat($entity->getLiter());
        self::assertInstanceOf(\DateTime::class, $entity->getDateTime());

        $json = $entity->jsonSerialize();
        self::assertIsArray($json);
        self::assertArrayNotHasKey('id', $json);
        self::assertArrayHasKey('liter', $json);
        self::assertArrayHasKey('datetime', $json);
    }
}

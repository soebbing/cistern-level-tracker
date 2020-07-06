<?php declare(strict_types=1);

namespace App\Tests\Repository;

use App\DataFixtures\LevelFixtures;
use App\Entity\Level;
use App\Repository\LevelRepository;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @IgnoreAnnotation("dataProvider")
 */
class LevelRepositoryTest extends WebTestCase
{
    use FixturesTrait;

    public function setUp(): void
    {
        $this->loadFixtures([
            LevelFixtures::class,
        ]);

        self::bootKernel();
    }

    public function testValuesAreStored()
    {
        $repository = static::$container->get(LevelRepository::class);

        $date = new \DateTimeImmutable('now');

        $repository->addEntry(4, $date);
        $results = $repository->getDataSince($date);

        $this->assertCount(1, $results);

        /** @var Level $result */
        $result = \array_shift($results);
        $this->assertInstanceOf(Level::class, $result);
        $this->assertEquals(4, $result->getLiter());
        $this->assertEquals($date->getTimestamp(), $result->getDatetime()->getTimestamp());
    }

    public function testDataSinceWithoutParameterFilters()
    {
        $repository = static::$container->get(LevelRepository::class);

        // Without defining a date, we get the results of last month
        $results = $repository->getDataSince();
        $this->assertCount(2, $results);
    }

    public function testDataSinceWithDateParameterWorks()
    {
        $repository = static::$container->get(LevelRepository::class);

        $lastMonth = (new \DateTimeImmutable('now'))->sub(new \DateInterval('P10Y'));
        $results = $repository->getDataSince($lastMonth);
        $this->assertCount(4, $results);
    }

    public function testNegativeLitersThrowException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $repository = static::$container->get(LevelRepository::class);

        $repository->addEntry(-4, new \DateTimeImmutable('now'));
    }

    public function testGetExportResultWorks()
    {
        $repository = static::$container->get(LevelRepository::class);
        $export = $repository->getAllResults();

        $this->assertContainsOnlyInstancesOf(Level::class, $export);
    }

    public function testEntity()
    {
        $repository = static::$container->get(LevelRepository::class);
        $export = $repository->getAllResults();

        /** @var Level $entity */
        $entity = \array_shift($export);
        $this->assertIsInt($entity->getId());
        $this->assertIsFloat($entity->getLiter());
        $this->assertInstanceOf(\DateTime::class, $entity->getDateTime());

        $json = $entity->jsonSerialize();
        $this->assertIsArray($json);
        $this->assertArrayNotHasKey('id', $json);
        $this->assertArrayHasKey('liter', $json);
        $this->assertArrayHasKey('datetime', $json);
    }
}

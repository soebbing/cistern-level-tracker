<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Level;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LevelFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create two entities in a known timeframe
        $manager->persist(
            new Level(5, new \DateTimeImmutable('01-01-2011 00:00:00', new \DateTimeZone('UTC')))
        );

        $manager->persist(
            new Level(10, new \DateTimeImmutable('01-01-2012 00:00:00', new \DateTimeZone('UTC')))
        );

        // Create two others in the last 30 days
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $first = $date->sub(new \DateInterval('P20D'));
        $second = $date->sub(new \DateInterval('P10D'));

        $manager->persist(
            new Level(5, $first)
        );

        $manager->persist(
            new Level(5, $second)
        );

        $manager->flush();
    }
}

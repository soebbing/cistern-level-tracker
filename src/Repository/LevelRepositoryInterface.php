<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Level;

interface LevelRepositoryInterface
{
    /**
     * @return Level[]
     */
    public function getAllResults(): array;

    /**
     * @return Level[]
     */
    public function getDataSince(\DateTimeInterface $since = null): array;

    public function addEntry(float $liter, \DateTimeInterface $dateTime): Level;
}

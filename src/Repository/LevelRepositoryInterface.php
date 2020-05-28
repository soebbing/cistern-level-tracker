<?php

namespace App\Repository;

use App\Entity\Level;

interface LevelRepositoryInterface
{
    /**
     * @return Level[]
     */
    public function getExportData(): array;

    /**
     * @return Level[]
     */
    public function getChartData(\DateTimeInterface $since = null): array;

    public function addEntry(float $liter, \DateTimeInterface $dateTime): void;
}
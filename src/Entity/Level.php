<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LevelRepository")
 */
class Level implements \JsonSerializable
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private float $liter;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private \DateTimeInterface $datetime;

    public function __construct(float $liter, \DateTimeInterface $datetime)
    {
        $this->liter = $liter;
        $this->datetime = $datetime;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLiter(): float
    {
        return $this->liter;
    }

    public function getDateTime(): \DateTimeInterface
    {
        return $this->datetime;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'liter' => $this->liter,
            'datetime' => $this->datetime,
        ];
    }

    public function setLiter(float $liter): void
    {
        $this->liter = $liter;
    }

    public function setDatetime(\DateTimeInterface $datetime): void
    {
        $this->datetime = $datetime;
    }
}

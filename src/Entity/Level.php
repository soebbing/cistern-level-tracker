<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="LevelRepository")
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

    public function setLiter(float $liter): self
    {
        $this->liter = $liter;

        return $this;
    }

    public function getDatetime(): \DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
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
}

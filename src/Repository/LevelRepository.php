<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Level;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;

class LevelRepository extends ServiceEntityRepository implements LevelRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Level::class);
    }

    /**
     * @inherited
     */
    public function getAllResults(): array
    {
        return $this->getDataSince(new \DateTimeImmutable('01-01-1970', new \DateTimeZone('UTC')));
    }

    /**
     * @inherited
     */
    public function getDataSince(\DateTimeInterface $since = null): array
    {
        if (!$since) {
            $since = (new \DateTime('now', new \DateTimeZone('UTC')))
                ->sub(new \DateInterval('P30D'));
        }

        $query = $this->createQueryBuilder('l')
            ->where('l.datetime >= :since')
            ->orderBy('l.datetime', 'ASC')
            ->setMaxResults(30)
            ->setParameter('since', $since)
            ->getQuery();

        return $query
            ->getResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function addEntry(float $liter, \DateTimeInterface $dateTime): Level
    {
        if ($liter < 0) {
            throw new \InvalidArgumentException('$liter cannot be negative');
        }

        $level = new Level($liter, $dateTime);
        $this->getEntityManager()->persist($level);
        $this->getEntityManager()->flush();

        return $level;
    }
}

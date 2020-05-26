<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Level;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;

class LevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Level::class);
    }

    public function getChartData(\DateTimeInterface $since = null)
    {
        if (!$since) {
            $since = new \DateTime();
            $since->sub(new \DateInterval('P30D'));
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

    public function addEntry(float $liter, \DateTimeInterface $dateTime)
    {
        $this->getEntityManager()->persist(new Level($liter, $dateTime));
        $this->getEntityManager()->flush();
    }
}

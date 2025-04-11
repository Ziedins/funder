<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExchangeRate>
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function findExchangeRate(int $baseCurrencyId, int $targetCurrencyId, bool $strictlyToday = false): ?ExchangeRate
    {
        if ($strictlyToday) {
            return $this->createQueryBuilder('r')
                ->andWhere('r.baseCurrency = :baseCurrency')
                ->setParameter('baseCurrency', $baseCurrencyId)
                ->andWhere('r.targetCurrency = :targetCurrency')
                ->setParameter('targetCurrency', $targetCurrencyId)
                ->andWhere('r.updatedAt > :updatedAt')
                ->setParameter('updatedAt', date_format(new \DateTime(), 'Y-m-d'))
                ->getQuery()
                ->getOneOrNullResult();
        } else {
            return $this->findOneBy(['baseCurrency' => $baseCurrencyId, 'targetCurrency' => $targetCurrencyId]);
        }
    }

//    /**
//     * @return ExchangeRate[] Returns an array of ExchangeRate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ExchangeRate
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

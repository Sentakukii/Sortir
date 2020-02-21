<?php

namespace App\Repository;

use App\Entity\EventArchive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EventArchive|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventArchive|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventArchive[]    findAll()
 * @method EventArchive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventArchiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventArchive::class);
    }

    // /**
    //  * @return EventArchive[] Returns an array of EventArchive objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventArchive
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

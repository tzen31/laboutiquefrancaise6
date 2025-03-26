<?php

namespace App\Repository;

use App\Entity\Wisslists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wisslists>
 *
 * @method Wisslists|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wisslists|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wisslists[]    findAll()
 * @method Wisslists[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WisslistsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wisslists::class);
    }

//    /**
//     * @return Wisslists[] Returns an array of Wisslists objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Wisslists
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

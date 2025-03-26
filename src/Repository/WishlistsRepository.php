<?php

namespace App\Repository;

use App\Entity\Wishlists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wishlists>
 *
 * @method Wishlists|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wishlists|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wishlists[]    findAll()
 * @method Wishlists[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishlistsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wishlists::class);
    }

//    /**
//     * @return Wishlists[] Returns an array of Wishlists objects
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

//    public function findOneBySomeField($value): ?Wishlists
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

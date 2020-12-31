<?php

namespace App\Repository;

use App\Entity\ChatDictionary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatDictionary|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatDictionary|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatDictionary[]    findAll()
 * @method ChatDictionary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatDictionaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatDictionary::class);
    }

    // /**
    //  * @return ChatDictionary[] Returns an array of ChatDictionary objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChatDictionary
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

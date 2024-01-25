<?php

namespace App\Repository;

use App\Entity\UserMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserMessage>
 *
 * @method UserMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMessage[]    findAll()
 * @method UserMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMessage::class);
    }

//    /**
//     * @return UserMessage[] Returns an array of UserMessage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    /**
     * @param $userId
     * @return UserMessage|null
     * @throws NonUniqueResultException
     */
    public function findOneByUserId($userId): ?UserMessage
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.userId = :val')
            ->setParameter('val', $userId)
            ->andWhere('u.processed_at IS NULL')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return UserMessage[]
     */
    public function getSomeResults(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.processed_at IS NOT NULL')
            ->addOrderBy('u.userId', 'asc')
            ->addOrderBy('u.message', 'asc')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult() ?? [];
    }
}

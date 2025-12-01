<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findCommentsMadeByUser(string $userName): array
    {
        return $this->createQueryBuilder('comments')
            ->join('comments.work', 'work')
            ->addSelect('work')
            ->join('comments.user', 'user')
            ->andWhere('user.userName = :val')
            ->setParameter('val', $userName)
            ->orderBy('comments.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function hideAllCommentsByUser(User $user): void
    {
        $this->createQueryBuilder('c')
            ->update()
            ->set('c.isPublic', ':status')
            ->where('c.user = :user')
            ->setParameter('status', false)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    //    /**
    //     * @return Comment[] Returns an array of Comment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Comment
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

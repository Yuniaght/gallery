<?php

namespace App\Repository;

use App\Entity\Work;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Work>
 */
class WorkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Work::class);
    }

    public function knpFindAll(): QueryBuilder
    {
        return $this->createQueryBuilder('works')
                    ->orderBy('works.createdAt', 'DESC');
    }

    public function knpFindByCategory(Category $category): QueryBuilder
    {
        return $this->createQueryBuilder('works')
            ->andWhere('works.category = :category')
            ->setParameter('category', $category)
            ->orderBy('works.createdAt', 'DESC');
    }

    public function UserFavoriteWork(string $userName)
    {
        return $this->createQueryBuilder('works')
            ->join('works.favorite', 'user')
            ->andWhere('user.userName = :val')
            ->setParameter('val', $userName)
            ->orderBy('works.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
    //  * @return ArticleFixture[] Returns an array of ArticleFixture objects
    //  */
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneBySomeField($value): ?ArticleFixture
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findLatest(int $page = 1, Category $category = null): Paginator
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('u', 'c')
            ->innerJoin('a.user', 'u')
            ->leftJoin('a.category', 'c')
            ->where('a.publishedAt <= :now')
            ->orderBy('a.publishedAt', 'DESC')
            ->setParameter('now', new \DateTime())
        ;

        if (null !== $category) {
            $qb->andWhere(':category MEMBER OF a.category_id')
                ->setParameter('category_id', $category);
        }

        return (new Paginator($qb))->paginate($page);
    }

    public function findUserArticles (int $page = 1, User $user) : Paginator
    {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.user', 'u')
            ->where('a.publishedAt <= :now')
            ->andWhere('a.user = :user')
            ->orderBy('a.publishedAt', 'DESC')
            ->setParameter('now', new \DateTime())
            ->setParameter('user', $user)
        ;

        return (new Paginator($qb))->paginate($page);
    }

}

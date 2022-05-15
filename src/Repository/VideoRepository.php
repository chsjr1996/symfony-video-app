<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Video>
 *
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(private PaginatorInterface $paginator, ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Video $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Video $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByChildIds(array $categoryIds, int $page, ?string $sortBy): PaginationInterface
    {
        $sortMethod = $sortBy != 'rating' ? $sortBy : 'ASC';

        $query = $this->createQueryBuilder('v')
            ->andWhere('v.category IN (:categoryIds)')
            ->setParameter('categoryIds', $categoryIds)
            ->orderBy('v.title', $sortMethod)
            ->getQuery();

        return $this->paginator->paginate($query, $page, 5);
    }

    public function findByTitle(string $searchQuery, int $page, ?string $sortBy): PaginationInterface
    {
        $sortMethod = $sortBy != 'rating' ? $sortBy : 'ASC';

        $queryBuilder = $this->createQueryBuilder('v');
        $searchTerms = $this->prepareQuery($searchQuery);

        foreach ($searchTerms as $key => $term) {
            $queryBuilder->orWhere('LOWER(v.title) LIKE :t_' . $key)
                ->setParameter('t_' . $key, '%' . strtolower(trim($term)) . '%');
        }

        $query = $queryBuilder->orderBy('v.title', $sortMethod);
        return $this->paginator->paginate($query, $page, 5);
    }

    /**
     * @return Video
     */
    public function videoDetails(int $id)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.comments', 'c')
            ->leftJoin('c.owner', 'u')
            ->addSelect('c', 'u')
            ->where('v.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function prepareQuery(string $searchQuery): array
    {
        return explode(' ', $searchQuery);
    }
}

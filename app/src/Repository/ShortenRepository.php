<?php
/**
 * Shorten repository.
 */

namespace App\Repository;

use App\Entity\Shorten;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ShortenRepository.
 *
 * @method Shorten|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shorten|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shorten[]    findAll()
 * @method Shorten[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Shorten>
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 */
class ShortenRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in configuration files.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shorten::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('shorten.addDate', 'DESC');
    }

    /**
     * Function save.
     *
     * @param Shorten $shorten Shorten to save
     */
    public function save(Shorten $shorten): void
    {
        $this->_em->persist($shorten);
        $this->_em->flush();
    }

    /**
     * Function delete.
     *
     * @param Shorten $shorten Shorten to remove
     *
     * @return void
     */
    public function delete(Shorten $shorten)
    {
        $this->_em->remove($shorten);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('shorten');
    }
}

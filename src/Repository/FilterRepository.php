<?php

namespace App\Repository;

use App\Utils\Repository\WhereBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Carteira.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
abstract class FilterRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, $this->getEntityClass());
    }

    abstract public function getEntityClass();

    public function findByFilters($filters, $orders = null, $limit = 100)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('e')->from($this->getEntityClass(), 'e');

        WhereBuilder::build($qb, $filters);
        $dql = $qb->getDql();
        $sql = $qb->getQuery()->getSQL();
        $query = $qb->getQuery();
        $query->setMaxResults($limit);
        return $query->execute();
    }
}

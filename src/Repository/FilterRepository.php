<?php

namespace App\Repository;

use App\Utils\Repository\WhereBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        $qb->from($this->getEntityClass(), 'e');
    }

    public function getDefaultOrders()
    {
        return array(
            ['column' => 'updated', 'dir' => 'desc']
        );
    }

    public function countByFilters($filters)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(e.id)');
        $this->handleFrombyFilters($qb);
        WhereBuilder::build($qb, $filters);
        $dql = $qb->getDql();
        $sql = $qb->getQuery()->getSQL();
        $count = $qb->getQuery()->getScalarResult();
        return $count[0][1];
    }

    public function findByFilters($filters, $orders = null, $start = 0, $limit = 10)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e');
        $this->handleFrombyFilters($qb);
        WhereBuilder::build($qb, $filters);
        if (!$orders) {
            $orders = $this->getDefaultOrders();
        }
        foreach ($orders as $order) {
            $qb->addOrderBy($order['column'], $order['dir']);
        }

        $dql = $qb->getDql();
        $sql = $qb->getQuery()->getSQL();
        $query = $qb->getQuery();
        $query->setFirstResult($start);
        $query->setMaxResults($limit);
        return $query->execute();
    }
}

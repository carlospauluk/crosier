<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Carteira;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Utils\Repository\WhereBuilder;

/**
 * Repository para a entidade Carteira.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class CarteiraRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carteira::class);
    }

    public function findByFilters($filters, $orders = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('e')->from('App\Entity\Financeiro\Carteira', 'e');
        
        WhereBuilder::build($qb, $filters);
        
        $dql = $qb->getDql();
        
        $sql = $qb->getQuery()->getSQL();
        
        // example5: retrieve the associated Query object with the processed DQL
        $query = $qb->getQuery();
        
        return $query->execute();
    }
}

<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Movimentacao;
use App\Utils\Repository\WhereBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Movimentacao.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class MovimentacaoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Movimentacao::class);
    }
    
    public function findByFilters($filters, $orders = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('e')->from('App\Entity\Financeiro\Movimentacao', 'e');
        
        WhereBuilder::build($qb, $filters);
        
        $dql = $qb->getDql();
        
        $sql = $qb->getQuery()->getSQL();
        
        // example5: retrieve the associated Query object with the processed DQL
        $qb->setMaxResults(200);
        $query = $qb->getQuery();
        
        return $query->execute();
    }

    public function findFirsts($max)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('e')->from('App\Entity\Financeiro\Movimentacao', 'e');

        $qb->setMaxResults($max);
        
        // example5: retrieve the associated Query object with the processed DQL
        $query = $qb->getQuery();
        
        return $query->execute();
    }
}

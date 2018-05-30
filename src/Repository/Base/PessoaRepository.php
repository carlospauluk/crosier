<?php
namespace App\Repository\Base;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Base\Pessoa;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Repository para a entidade Pessoa.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class PessoaRepository extends ServiceEntityRepository
{

    private $logger;
    
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Pessoa::class);
        $this->logger = $logger;
    }

    public function findAllByNome($str)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
//         $qb->select('e')
//             ->from('App\Entity\Financeiro\Movimentacao', 'e')
//             ->join('App\Entity\Base\Pessoa', 'p', Join::WITH, 'e.pessoa = p')
//             ->where('p.nome LIKE :str OR p.nomeFantasia LIKE :str');
        
        $qb->select('e.id, e.documento, e.nome, e.nomeFantasia')
            ->from('App\Entity\Base\Pessoa', 'e')
            ->where('e.nome LIKE :str OR e.nomeFantasia LIKE :str');
        
        $qb->setParameter("str", "%" . $str . "%");
        
        // $dql = $qb->getDql();
        
        // $sql = $qb->getQuery()->getSQL();
        
        $qb->setMaxResults(20);
        
        $query = $qb->getQuery();
        
        return $query->execute();
    }
    
    public function findAllByNomez($str)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
//         $qb->select('e')
//             ->from('App\Entity\Financeiro\Movimentacao', 'e')
//             ->join('App\Entity\Base\Pessoa', 'p', Join::WITH, 'e.pessoa = p')
//             ->where('p.nome LIKE :str OR p.nomeFantasia LIKE :str');
        
        $qb->select('e')
            ->from('App\Entity\Base\Pessoa', 'e')
            ->where('e.nome LIKE :str OR e.nomeFantasia LIKE :str');
        
        $qb->setParameter("str", "%" . $str . "%");
        
        // $dql = $qb->getDql();
        
        // $sql = $qb->getQuery()->getSQL();
        
        $qb->setMaxResults(20);
        
        $query = $qb->getQuery();
        
        return $query->execute();
    }
}

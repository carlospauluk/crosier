<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\CentroCusto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade CentroCusto.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class CentroCustoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CentroCusto::class);
    }

    
}

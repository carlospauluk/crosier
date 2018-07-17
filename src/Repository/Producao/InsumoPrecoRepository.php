<?php
namespace App\Repository\Producao;

use App\Entity\Producao\InsumoPreco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade InsumoPreco.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class InsumoPrecoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InsumoPreco::class);
    }
    
}

<?php
namespace App\Repository\Producao;

use App\Entity\Producao\Insumo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Insumo.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class InsumoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Insumo::class);
    }
}

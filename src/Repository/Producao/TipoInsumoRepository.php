<?php
namespace App\Repository\Producao;

use App\Entity\Producao\TipoInsumo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade TipoInsumo.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class TipoInsumoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoInsumo::class);
    }
}

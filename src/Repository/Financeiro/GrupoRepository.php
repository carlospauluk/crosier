<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Grupo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Grupo.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class GrupoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grupo::class);
    }
}

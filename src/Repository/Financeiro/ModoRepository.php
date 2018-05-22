<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Modo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Modo.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ModoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Modo::class);
    }
}

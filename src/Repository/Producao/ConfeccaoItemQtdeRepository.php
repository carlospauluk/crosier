<?php
namespace App\Repository\Producao;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\Producao\ConfeccaoItemQtde;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade ConfeccaoItemQtde.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ConfeccaoItemQtdeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfeccaoItemQtde::class);
    }
    
}

<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\DepreciacaoPreco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade ConfeccaoItem.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class DepreciacaoPrecoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepreciacaoPreco::class);
    }
}

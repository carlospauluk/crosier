<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\GrupoItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade GrupoItem.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class GrupoItemRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrupoItem::class);
    }
}

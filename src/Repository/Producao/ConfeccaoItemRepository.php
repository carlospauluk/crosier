<?php
namespace App\Repository\Producao;

use App\Entity\Producao\ConfeccaoItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade ConfeccaoItem.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ConfeccaoItemRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfeccaoItem::class);
    }
}

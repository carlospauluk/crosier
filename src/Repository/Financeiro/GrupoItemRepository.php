<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\GrupoItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade GrupoItem.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class GrupoItemRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GrupoItem::class);
    }
}

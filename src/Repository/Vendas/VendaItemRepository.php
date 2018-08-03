<?php
namespace App\Repository\Vendas;

use App\Entity\Vendas\VendaItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade VendaItem.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class VendaItemRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VendaItem::class);
    }
}

<?php
namespace App\Repository\Fiscal;

use App\Entity\Fiscal\NotaFiscalItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade NotaFiscalItem.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class NotaFiscalItemRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NotaFiscalItem::class);
    }
}

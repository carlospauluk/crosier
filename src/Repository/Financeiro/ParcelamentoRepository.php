<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Parcelamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Parcelamento.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ParcelamentoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parcelamento::class);
    }
}
        
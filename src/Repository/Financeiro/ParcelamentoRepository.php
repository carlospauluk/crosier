<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Parcelamento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Parcelamento.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ParcelamentoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Parcelamento::class);
    }
}
        
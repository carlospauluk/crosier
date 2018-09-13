<?php

namespace App\Repository\Vendas;

use App\Entity\Vendas\TipoVenda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade TipoVenda.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class TipoVendaRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoVenda::class);
    }
}

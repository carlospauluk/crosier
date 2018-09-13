<?php

namespace App\Repository\Producao;

use App\Entity\Producao\InsumoPreco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade InsumoPreco.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class InsumoPrecoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InsumoPreco::class);
    }

}

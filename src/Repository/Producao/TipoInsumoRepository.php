<?php

namespace App\Repository\Producao;

use App\Entity\Producao\TipoInsumo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade TipoInsumo.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class TipoInsumoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoInsumo::class);
    }
}

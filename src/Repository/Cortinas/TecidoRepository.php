<?php

namespace App\Repository\Cortinas;

use App\Entity\Cortinas\Tecido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Tecido.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class TecidoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tecido::class);
    }
}

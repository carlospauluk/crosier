<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\FornecedorTipo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade FornecedorTipo.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class FornecedorTipoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FornecedorTipo::class);
    }
}

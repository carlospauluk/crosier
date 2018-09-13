<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\UnidadeProduto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade UnidadeProduto.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class UnidadeProdutoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UnidadeProduto::class);
    }
}

<?php

namespace App\Repository\OC;

use App\EntityOC\OcProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade OcProduct.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcProductRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OcProduct::class);
    }

}

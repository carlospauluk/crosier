<?php

namespace App\OC\Repository;

use App\OC\Entity\OcProduct;
use App\OC\Entity\OcProductDescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade OcProductDescription.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcProductDescriptionRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OcProductDescription::class);
    }

}

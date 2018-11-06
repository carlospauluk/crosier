<?php

namespace App\OC\Repository;

use App\OC\Entity\OcManufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade OcManufacturer.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcManufacturerRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OcManufacturer::class);
    }

}

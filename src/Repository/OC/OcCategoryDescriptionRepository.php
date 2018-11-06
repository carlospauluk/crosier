<?php

namespace App\OC\Repository;

use App\OC\Entity\OcCategoryDescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade OcCategoryDescription.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcCategoryDescriptionRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OcCategoryDescription::class);
    }

}

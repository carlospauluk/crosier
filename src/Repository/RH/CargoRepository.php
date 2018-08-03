<?php
namespace App\Repository\RH;

use App\Entity\RH\Cargo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Cargo.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class CargoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cargo::class);
    }
}

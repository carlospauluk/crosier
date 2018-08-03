<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Subdepto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Subdepto.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class SubdeptoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Subdepto::class);
    }
}

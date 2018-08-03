<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Depto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Depto.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class DeptoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Depto::class);
    }
}

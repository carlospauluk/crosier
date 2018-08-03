<?php
namespace App\Repository\RH;

use App\Entity\RH\FuncionarioCargo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade FuncionarioCargo.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class FuncionarioCargoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FuncionarioCargo::class);
    }
}

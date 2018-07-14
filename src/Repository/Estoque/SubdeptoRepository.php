<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Subdepto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Subdepto.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class SubdeptoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subdepto::class);
    }
}

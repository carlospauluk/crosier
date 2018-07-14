<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Grade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Grade.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class GradeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grade::class);
    }
}

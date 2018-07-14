<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\GradeTamanho;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade GradeTamanho.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class GradeTamanhoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeTamanho::class);
    }
}

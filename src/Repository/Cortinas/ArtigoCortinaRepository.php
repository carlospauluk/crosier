<?php
namespace App\Repository\Cortinas;

use App\Entity\Cortinas\ArtigoCortina;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade ArtigoCortina.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ArtigoCortinaRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArtigoCortina::class);
    }
}

<?php
namespace App\Repository\Cortinas;

use App\Entity\Cortinas\ArtigoCortina;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade ArtigoCortina.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ArtigoCortinaRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ArtigoCortina::class);
    }
}

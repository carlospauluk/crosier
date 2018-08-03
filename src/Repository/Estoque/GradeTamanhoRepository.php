<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\GradeTamanho;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade GradeTamanho.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class GradeTamanhoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GradeTamanho::class);
    }
}

<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Cadeia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Cadeia    .
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class CadeiaRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cadeia::class);
    }
}

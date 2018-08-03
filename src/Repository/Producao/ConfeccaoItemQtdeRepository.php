<?php
namespace App\Repository\Producao;

use App\Entity\Producao\ConfeccaoItemQtde;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade ConfeccaoItemQtde.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ConfeccaoItemQtdeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConfeccaoItemQtde::class);
    }
    
}

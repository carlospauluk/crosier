<?php
namespace App\Repository\CRM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\CRM\Cliente;

/**
 * Repository para a entidade Cliente.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ClienteRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cliente::class);
    }
}

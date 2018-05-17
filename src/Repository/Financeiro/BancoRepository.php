<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Banco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Banco.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class BancoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banco::class);
    }
}

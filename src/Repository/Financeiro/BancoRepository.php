<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Banco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Banco.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class BancoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Banco::class);
    }
}

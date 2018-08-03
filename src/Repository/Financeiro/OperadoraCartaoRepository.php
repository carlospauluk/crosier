<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\OperadoraCartao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade OperadoraCartao.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class OperadoraCartaoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OperadoraCartao::class);
    }
}
    
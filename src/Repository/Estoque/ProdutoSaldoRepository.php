<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\ProdutoSaldo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade ProdutoSaldo.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ProdutoSaldoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProdutoSaldo::class);
    }
}

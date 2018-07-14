<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Produto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Produto.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ProdutoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produto::class);
    }
}

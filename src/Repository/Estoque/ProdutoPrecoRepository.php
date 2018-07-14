<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\ProdutoPreco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade ProdutoPreco.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ProdutoPrecoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProdutoPreco::class);
    }
}

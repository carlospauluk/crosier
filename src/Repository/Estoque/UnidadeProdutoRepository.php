<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\UnidadeProduto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade UnidadeProduto.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class UnidadeProdutoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnidadeProduto::class);
    }
}

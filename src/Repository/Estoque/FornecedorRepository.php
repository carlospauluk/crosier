<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Fornecedor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Fornecedor.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class FornecedorRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fornecedor::class);
    }
}

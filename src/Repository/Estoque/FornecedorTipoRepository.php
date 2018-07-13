<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\FornecedorTipo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade FornecedorTipo.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class FornecedorTipoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FornecedorTipo::class);
    }
}

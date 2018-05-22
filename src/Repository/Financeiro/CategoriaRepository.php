<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Categoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Banco.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class CategoriaRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categoria::class);
    }
}

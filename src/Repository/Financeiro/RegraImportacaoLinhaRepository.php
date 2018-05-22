<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\RegraImportacaoLinha;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade RegraImportacaoLinha.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class RegraImportacaoLinhaRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegraImportacaoLinha::class);
    }
}
                        
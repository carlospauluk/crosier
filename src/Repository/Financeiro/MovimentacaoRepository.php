<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Movimentacao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;


/**
 * Repository para a entidade Movimentacao.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class MovimentacaoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movimentacao::class);
    }
}

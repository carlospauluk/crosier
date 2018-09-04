<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Movimentacao;
use App\Repository\FilterRepository;
use App\Utils\Repository\WhereBuilder;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Movimentacao.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class MovimentacaoRepository extends FilterRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Movimentacao::class);
    }

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->leftJoin('App\Entity\Base\Pessoa', 'p', 'WITH', 'e.pessoa = p')
            ->join('App\Entity\Financeiro\Carteira', 'cart', 'WITH', 'e.carteira = cart')
            ->join('App\Entity\Financeiro\Categoria', 'categ', 'WITH', 'e.categoria = categ')
            ->join('App\Entity\Financeiro\CentroCusto', 'cc', 'WITH', 'e.centroCusto = cc')
            ->join('App\Entity\Financeiro\Modo', 'modo', 'WITH', 'e.modo = modo');
    }


    public function getEntityClass()
    {
        return Movimentacao::class;
    }
}

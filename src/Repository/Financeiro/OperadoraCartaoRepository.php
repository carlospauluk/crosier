<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\OperadoraCartao;
use App\Repository\FilterRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Repository para a entidade OperadoraCartao.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OperadoraCartaoRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OperadoraCartao::class;
    }

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->join('App\Entity\Financeiro\Carteira', 'c', 'WITH', 'e.carteira = c');
    }
}
    
<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\BandeiraCartao;
use App\Repository\FilterRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Repository para a entidade BandeiraCartao.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class BandeiraCartaoRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return BandeiraCartao::class;
    }

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->join('App\Entity\Financeiro\Modo','m','WITH','e.modo = m');
    }
}

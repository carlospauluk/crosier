<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\OperadoraCartao;
use App\Repository\FilterRepository;

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
}
    
<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\BandeiraCartao;
use App\Repository\FilterRepository;

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
}

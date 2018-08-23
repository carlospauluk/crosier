<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Banco;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade Banco.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class BancoRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return Banco::class;
    }
}

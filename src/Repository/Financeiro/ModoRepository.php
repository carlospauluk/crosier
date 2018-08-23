<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Modo;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade Modo.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class ModoRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return Modo::class;
    }
}

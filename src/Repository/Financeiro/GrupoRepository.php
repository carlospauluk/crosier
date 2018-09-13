<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Grupo;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade Grupo.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class GrupoRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return Grupo::class;
    }
}

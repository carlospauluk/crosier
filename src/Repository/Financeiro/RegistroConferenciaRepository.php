<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\RegistroConferencia;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade RegistroConferencia.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RegistroConferenciaRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return RegistroConferencia::class;
    }
}
            
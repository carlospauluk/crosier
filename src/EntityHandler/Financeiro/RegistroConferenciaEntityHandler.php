<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\RegistroConferencia;
use App\EntityHandler\EntityHandler;

class RegistroConferenciaEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return RegistroConferencia::class;
    }
}
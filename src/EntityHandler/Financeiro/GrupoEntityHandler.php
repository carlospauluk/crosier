<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\Grupo;
use App\EntityHandler\EntityHandler;

class GrupoEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Grupo::class;
    }
}
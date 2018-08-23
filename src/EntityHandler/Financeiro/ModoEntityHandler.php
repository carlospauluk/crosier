<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\Modo;
use App\EntityHandler\EntityHandler;

class ModoEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Modo::class;
    }
}
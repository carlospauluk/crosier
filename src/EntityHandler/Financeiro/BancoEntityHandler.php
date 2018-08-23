<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\Banco;
use App\EntityHandler\EntityHandler;

class BancoEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Banco::class;
    }
}
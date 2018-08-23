<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\CentroCusto;
use App\EntityHandler\EntityHandler;

class CentroCustoEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return CentroCusto::class;
    }
}
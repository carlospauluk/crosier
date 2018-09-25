<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\Cadeia;
use App\EntityHandler\EntityHandler;

class CadeiaEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Cadeia::class;
    }
}
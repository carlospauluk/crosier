<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\Carteira;
use App\EntityHandler\EntityHandler;

class CarteiraEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Carteira::class;
    }
}
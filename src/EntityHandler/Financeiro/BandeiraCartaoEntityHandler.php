<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\BandeiraCartao;
use App\EntityHandler\EntityHandler;

class BandeiraCartaoEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return BandeiraCartao::class;
    }
}
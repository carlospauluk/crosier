<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\OperadoraCartao;
use App\EntityHandler\EntityHandler;

class OperadoraCartaoEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return OperadoraCartao::class;
    }
}
<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\EntityHandler;

class MovimentacaoEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Movimentacao::class;
    }
}
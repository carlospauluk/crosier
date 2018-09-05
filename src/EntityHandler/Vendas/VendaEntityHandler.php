<?php

namespace App\EntityHandler\Vendas;

use App\Entity\Vendas\Venda;
use App\EntityHandler\EntityHandler;

class VendaEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Venda::class;
    }
}
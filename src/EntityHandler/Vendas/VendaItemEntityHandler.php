<?php

namespace App\EntityHandler\Vendas;

use App\Entity\Vendas\VendaItem;
use App\EntityHandler\EntityHandler;

class VendaItemEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return VendaItem::class;
    }
}
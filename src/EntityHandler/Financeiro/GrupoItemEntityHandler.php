<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\Grupo;
use App\Entity\Financeiro\GrupoItem;
use App\EntityHandler\EntityHandler;

class GrupoItemEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return GrupoItem::class;
    }
}
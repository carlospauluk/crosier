<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\Categoria;
use App\EntityHandler\EntityHandler;

class CategoriaEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Categoria::class;
    }
}
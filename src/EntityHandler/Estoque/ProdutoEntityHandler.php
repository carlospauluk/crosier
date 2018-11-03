<?php

namespace App\EntityHandler\Estoque;

use App\Entity\Estoque\Produto;
use App\EntityHandler\EntityHandler;

class ProdutoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Produto::class;
    }
}
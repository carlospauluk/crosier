<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\RegraImportacaoLinha;
use App\EntityHandler\EntityHandler;

class RegraImportacaoLinhaEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return RegraImportacaoLinha::class;
    }
}
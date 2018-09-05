<?php

namespace App\EntityHandler\Fiscal;

use App\Entity\Fiscal\NotaFiscalHistorico;
use App\EntityHandler\EntityHandler;

class NotaFiscalHistoricoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return NotaFiscalHistorico::class;
    }
}
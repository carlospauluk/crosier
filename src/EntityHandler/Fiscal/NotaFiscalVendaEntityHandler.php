<?php

namespace App\EntityHandler\Fiscal;

use App\Entity\Fiscal\NotaFiscalVenda;
use App\EntityHandler\EntityHandler;

class NotaFiscalVendaEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return NotaFiscalVenda::class;
    }
}
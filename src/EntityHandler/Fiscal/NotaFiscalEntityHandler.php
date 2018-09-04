<?php

namespace App\EntityHandler\Fiscal;

use App\EntityHandler\EntityHandler;

class NotaFiscalEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return NotaFiscal::class;
    }
}
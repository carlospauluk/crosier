<?php

namespace App\EntityHandler\Fiscal;

use App\EntityHandler\EntityHandler;

class NotaFiscalItemEntityHandler extends EntityHandler
{

    public function beforeSave($nfItem)
    {
        if (!$nfItem->getOrdem()) {
            $ultimaOrdem = 0;
            foreach ($nfItem->getNotaFiscal()->getItens() as $item) {
                if ($item->getOrdem() > $ultimaOrdem) {
                    $ultimaOrdem = $item->getOrdem();
                }
            }
            $nfItem->setOrdem($ultimaOrdem + 1);
        }
        if (!$nfItem->getCsosn()) {
            $nfItem->setCsosn(103);
        }
    }

    public function getEntityClass()
    {
        return NotaFiscalItem::class;
    }
}
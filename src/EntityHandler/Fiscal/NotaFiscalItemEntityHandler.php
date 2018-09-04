<?php

namespace App\EntityHandler\Fiscal;

use App\EntityHandler\EntityHandler;

class NotaFiscalItemEntityHandler extends EntityHandler
{

    public function beforePersist($nfItem)
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
    }

    public function getEntityClass()
    {
        return NotaFiscalItem::class;
    }
}
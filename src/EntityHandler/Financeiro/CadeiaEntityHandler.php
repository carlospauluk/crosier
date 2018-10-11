<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\Cadeia;
use App\EntityHandler\EntityHandler;

class CadeiaEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Cadeia::class;
    }

    public function beforeSave($cadeia)
    {
        if (!$cadeia->getUnqc()) {
            $cadeia->setUnqc(md5(uniqid(rand(), true)));
        }
    }

    public function corrigirUnqcs() {
        $cadeias = $this->getEntityManager()->getRepository(Cadeia::class)->findAll();
        foreach ($cadeias as $cadeia) {
            $this->save($cadeia);
        }
    }


}
<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\EntityHandler;

class MovimentacaoEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Movimentacao::class;
    }

    public function beforePersist($movimentacao)
    {
        if (!$movimentacao->getCentroCusto()) {
            $movimentacao->setCentroCusto($this->getEntityManager()->getRepository(CentroCusto::class)->find(1));
        }
    }

}
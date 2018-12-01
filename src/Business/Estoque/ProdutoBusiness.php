<?php

namespace App\Business\Estoque;

use App\Business\BaseBusiness;
use App\Entity\Estoque\Produto;

class ProdutoBusiness extends BaseBusiness
{

    public function ehUniformeEscolar(Produto $produto)
    {
        return $produto and $produto->getSubdepto() and $produto->getSubdepto()->getDepto() and $produto->getSubdepto()->getDepto()->getCodigo() == 1;
    }

}
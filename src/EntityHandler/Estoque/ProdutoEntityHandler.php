<?php

namespace App\EntityHandler\Estoque;

use App\Business\Estoque\ProdutoBusiness;
use App\Entity\Estoque\Produto;
use App\EntityHandler\EntityHandler;

class ProdutoEntityHandler extends EntityHandler
{

    private $produtoBusiness;

    public function beforeSave($produto)
    {
        $this->getProdutoBusiness()->saveOcProduct($produto, null);
    }

    /**
     * @return mixed
     */
    public function getProdutoBusiness():ProdutoBusiness
    {
        return $this->produtoBusiness;
    }

    /**
     * @required
     * @param mixed $produtoBusiness
     */
    public function setProdutoBusiness(ProdutoBusiness $produtoBusiness): void
    {
        $this->produtoBusiness = $produtoBusiness;
    }


    public function getEntityClass()
    {
        return Produto::class;
    }
}
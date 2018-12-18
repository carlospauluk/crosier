<?php

namespace App\EntityHandler\Estoque;

use App\Business\Estoque\OCBusiness;
use App\Entity\Estoque\Produto;
use App\EntityHandler\EntityHandler;

class ProdutoEntityHandler extends EntityHandler
{

    private $ocBusiness;

    public function beforeSave($produto)
    {
        if ($produto->getNaLojaVirtual() == true) {
            $this->getOcBusiness()->saveOcProduct($produto, null);
        }
    }

    /**
     * @return OCBusiness
     */
    public function getOcBusiness(): OCBusiness
    {
        return $this->ocBusiness;
    }

    /**
     * @required
     * @param OCBusiness $ocBusiness
     */
    public function setOcBusiness(OCBusiness $ocBusiness): void
    {
        $this->ocBusiness = $ocBusiness;
    }


    public function getEntityClass()
    {
        return Produto::class;
    }
}
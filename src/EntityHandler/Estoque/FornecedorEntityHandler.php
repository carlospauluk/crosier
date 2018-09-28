<?php

namespace App\EntityHandler\Estoque;

use App\Entity\CRM\Cliente;
use App\Entity\Estoque\Fornecedor;
use App\EntityHandler\EntityHandler;

class FornecedorEntityHandler extends EntityHandler
{

    public function beforeSave($fornecedor)
    {
        if (!$fornecedor->getCodigo()) {
            $codigo = $this->getEntityManager()->getRepository(Cliente::class)->findProximoCodigo();
            $fornecedor->setCodigo($codigo);
        }
    }


    public function getEntityClass()
    {
        return Fornecedor::class;
    }
}
<?php

namespace App\EntityHandler\CRM;

use App\Entity\CRM\Cliente;
use App\EntityHandler\EntityHandler;

class ClienteEntityHandler extends EntityHandler
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
        return Cliente::class;
    }
}
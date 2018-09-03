<?php

namespace App\EntityHandler\CRM;

use App\Entity\CRM\Cliente;
use App\EntityHandler\EntityHandler;

class ClienteEntityHandler extends EntityHandler
{

    public function beforePersist($cliente)
    {
        if (!$cliente->getCodigo()) {
            $codigo = $this->getEntityManager()->getRepository(Cliente::class)->findProximoCodigo();
            $cliente->setCodigo($codigo);
        }
    }


    public function getEntityClass()
    {
        return Cliente::class;
    }
}
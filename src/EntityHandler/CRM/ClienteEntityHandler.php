<?php

namespace App\EntityHandler\CRM;

use App\Entity\CRM\Cliente;
use App\EntityHandler\EntityHandler;

class ClienteEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Cliente::class;
    }
}
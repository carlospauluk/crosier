<?php

namespace App\EntityHandler\Base;

use App\Entity\Base\Endereco;
use App\Entity\Base\EntityId;
use App\EntityHandler\EntityHandler;

class EnderecoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Endereco::class;
    }

    public function persistWith(Endereco $endereco, EntityId $entityId) {

    }
}
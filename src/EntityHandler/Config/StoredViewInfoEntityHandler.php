<?php

namespace App\EntityHandler\Config;

use App\Entity\Config\StoredViewInfo;
use App\EntityHandler\EntityHandler;

class StoredViewInfoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return StoredViewInfo::class;
    }


}
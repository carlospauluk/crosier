<?php

namespace App\EntityHandler\Config;

use App\Entity\Config\Config;
use App\EntityHandler\EntityHandler;

class ConfigEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Config::class;
    }
}
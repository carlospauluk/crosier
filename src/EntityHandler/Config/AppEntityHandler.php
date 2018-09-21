<?php

namespace App\EntityHandler\Config;

use App\EntityHandler\EntityHandler;
use App\Entity\Config\App;

class AppEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return App::class;
    }
}
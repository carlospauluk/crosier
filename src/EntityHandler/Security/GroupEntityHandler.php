<?php

namespace App\EntityHandler\Security;

use App\Entity\Security\Group;
use App\EntityHandler\EntityHandler;

/**
 * Class GroupEntityHandler
 * @package App\EntityHandler\Security
 * @author Carlos Eduardo Pauluk
 */
class GroupEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Group::class;
    }
}
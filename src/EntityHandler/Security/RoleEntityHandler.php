<?php

namespace App\EntityHandler\Security;

use App\Entity\Security\Role;
use App\EntityHandler\EntityHandler;

/**
 * Class RoleEntityHandler
 * @package App\EntityHandler\Security
 * @author Carlos Eduardo Pauluk
 */
class RoleEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Role::class;
    }
}
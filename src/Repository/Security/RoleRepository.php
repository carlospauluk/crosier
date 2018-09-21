<?php

namespace App\Repository\Security;

use App\Entity\Security\Role;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade Role.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RoleRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return Role::class;
    }
}

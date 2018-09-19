<?php

namespace App\Repository\Config;

use App\Repository\FilterRepository;
use App\Entity\Config\App;

/**
 * Repository para a entidade App.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class AppRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return App::class;
    }
}

<?php

namespace App\Repository\OC;

use App\EntityOC\OcOption;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcOption.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcOptionRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcOption::class;
    }
}

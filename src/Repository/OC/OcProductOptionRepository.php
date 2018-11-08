<?php

namespace App\Repository\OC;

use App\EntityOC\OcProductOption;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcProductOption.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcProductOptionRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcProductOption::class;
    }
}

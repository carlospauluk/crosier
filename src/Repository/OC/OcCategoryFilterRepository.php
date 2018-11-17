<?php

namespace App\Repository\OC;

use App\EntityOC\OcCategoryFilter;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcCategoryFilter.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcCategoryFilterRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcCategoryFilter::class;
    }
}

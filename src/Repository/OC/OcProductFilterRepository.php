<?php

namespace App\Repository\OC;

use App\EntityOC\OcFilterDescription;
use App\EntityOC\OcProductFilter;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcProductFilter.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcProductFilterRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcProductFilter::class;
    }
}

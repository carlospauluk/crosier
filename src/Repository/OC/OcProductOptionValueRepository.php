<?php

namespace App\Repository\OC;

use App\EntityOC\OcProductOptionValue;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcProductOptionValue.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcProductOptionValueRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcProductOptionValue::class;
    }
}

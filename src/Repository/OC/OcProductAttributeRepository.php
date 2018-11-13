<?php

namespace App\Repository\OC;

use App\EntityOC\OcProductAttribute;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcProductAttribute.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcProductAttributeRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcProductAttribute::class;
    }
}

<?php

namespace App\Repository\OC;

use App\EntityOC\OcProduct;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcProduct.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcProductRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcProduct::class;
    }
}

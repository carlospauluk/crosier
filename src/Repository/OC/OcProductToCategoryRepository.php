<?php

namespace App\Repository\OC;

use App\EntityOC\OcProductToCategory;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcProductToCategory.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcProductToCategoryRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcProductToCategory::class;
    }
}

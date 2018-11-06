<?php

namespace App\Repository\OC;

use App\EntityOC\OcCategoryDescription;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcCategoryDescription.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcCategoryDescriptionRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcCategoryDescription::class;
    }
}

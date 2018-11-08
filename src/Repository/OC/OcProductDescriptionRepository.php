<?php

namespace App\Repository\OC;

use App\EntityOC\OcProductDescription;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcProductDescription.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcProductDescriptionRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcProductDescription::class;
    }
}

<?php

namespace App\Repository\OC;

use App\EntityOC\OcFilterGroupDescription;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcFilterGroupDescription.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcFilterGroupDescriptionRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcFilterGroupDescription::class;
    }
}

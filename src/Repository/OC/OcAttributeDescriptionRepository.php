<?php

namespace App\Repository\OC;

use App\EntityOC\OcAttributeDescription;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcAttributeDescription.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcAttributeDescriptionRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcAttributeDescription::class;
    }
}

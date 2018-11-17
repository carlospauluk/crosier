<?php

namespace App\Repository\OC;

use App\EntityOC\OcFilterDescription;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcFilterDescription.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcFilterDescriptionRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcFilterDescription::class;
    }
}

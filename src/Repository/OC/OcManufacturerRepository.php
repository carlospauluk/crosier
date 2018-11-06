<?php

namespace App\Repository\OC;

use App\EntityOC\OcManufacturer;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcManufacturer.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcManufacturerRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcManufacturer::class;
    }
}

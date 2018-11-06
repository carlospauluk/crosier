<?php

namespace App\Repository\OC;

use App\EntityOC\OcStockStatus;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcStockStatus.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcStockStatusRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcStockStatus::class;
    }
}

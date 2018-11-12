<?php

namespace App\Repository\OC;

use App\EntityOC\OcProductImage;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade OcProductImage.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class OcProductImageRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return OcProductImage::class;
    }
}

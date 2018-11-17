<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\SubdeptoOcCategory;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade SubdeptoOcCategory.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class SubdeptoOcCategoryRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return SubdeptoOcCategory::class;
    }
}

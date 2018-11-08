<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\GradeOcOption;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade GradeOcOption.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class GradeOcOptionRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return GradeOcOption::class;
    }
}

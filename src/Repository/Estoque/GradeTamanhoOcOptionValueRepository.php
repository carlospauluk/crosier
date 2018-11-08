<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\GradeTamanhoOcOptionValue;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade GradeTamanho.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class GradeTamanhoOcOptionValueRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return GradeTamanhoOcOptionValue::class;
    }
}

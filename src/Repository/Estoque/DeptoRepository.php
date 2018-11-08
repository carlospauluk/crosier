<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\Depto;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade Depto.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class DeptoRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return Depto::class;
    }
}

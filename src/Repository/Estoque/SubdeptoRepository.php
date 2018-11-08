<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\Subdepto;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade Subdepto.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class SubdeptoRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return Subdepto::class;
    }
}

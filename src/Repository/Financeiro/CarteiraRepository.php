<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Carteira;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade Carteira.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class CarteiraRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return Carteira::class;
    }


}

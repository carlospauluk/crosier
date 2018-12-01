<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\FornecedorOcManufacturer;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade FornecedorOcManufacturer.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class FornecedorOcManufacturerRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return FornecedorOcManufacturer::class;
    }
}

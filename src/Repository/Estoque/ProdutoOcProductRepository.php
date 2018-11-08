<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\ProdutoOcProduct;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade ProdutoOcProduct.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class ProdutoOcProductRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return ProdutoOcProduct::class;
    }
}

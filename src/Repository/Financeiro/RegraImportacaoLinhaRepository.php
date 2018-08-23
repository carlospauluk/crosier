<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\RegraImportacaoLinha;
use App\Repository\FilterRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade RegraImportacaoLinha.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RegraImportacaoLinhaRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return RegraImportacaoLinha::class;
    }
}
                        
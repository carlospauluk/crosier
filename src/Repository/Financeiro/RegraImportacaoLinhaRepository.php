<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Carteira;
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

    public function findAllBy(Carteira $carteira)
    {
        $ql = "SELECT r FROM RegraImportacaoLinha r WHERE "
            . "r.carteira IS NULL OR "
            . "r.carteiraDestino IS NULL OR "
            . "r.carteiraDestino = :carteira OR "
            . "r.carteira = :carteira";

        $qry = $this->getEntityManager()->createQuery($ql);
        $qry->setParameter('carteira', $carteira);
        return $qry->getResult();
    }
}
                        
<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\RegraImportacaoLinha;
use App\Repository\FilterRepository;
use Doctrine\ORM\QueryBuilder;

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

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->leftJoin('App\Entity\Financeiro\Carteira', 'carteira', 'WITH', 'e.carteira = carteira')
            ->leftJoin('App\Entity\Financeiro\Carteira', 'carteiraDestino', 'WITH', 'e.carteiraDestino = carteiraDestino')
            ->leftJoin('App\Entity\Financeiro\Modo', 'modo', 'WITH', 'e.modo = modo')
            ->leftJoin('App\Entity\Financeiro\CentroCusto', 'centroCusto', 'WITH', 'e.centroCusto = centroCusto')
            ->leftJoin('App\Entity\Financeiro\Categoria', 'categoria', 'WITH', 'e.categoria = categoria');
    }

    public function findAllBy(Carteira $carteira)
    {
        $ql = "SELECT r FROM App\Entity\Financeiro\RegraImportacaoLinha r WHERE "
            . "r.carteira IS NULL OR "
            . "r.carteiraDestino IS NULL OR "
            . "r.carteiraDestino = :carteira OR "
            . "r.carteira = :carteira";

        $qry = $this->getEntityManager()->createQuery($ql);
        $qry->setParameter('carteira', $carteira);
        return $qry->getResult();
    }
}
                        
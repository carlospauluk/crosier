<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\OperadoraCartao;
use App\Repository\FilterRepository;
use App\Utils\Repository\FilterData;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;

/**
 * Repository para a entidade Movimentacao.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class MovimentacaoRepository extends FilterRepository
{

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->leftJoin('App\Entity\Base\Pessoa', 'p', 'WITH', 'e.pessoa = p')
            ->join('App\Entity\Financeiro\Carteira', 'cart', 'WITH', 'e.carteira = cart')
            ->join('App\Entity\Financeiro\Categoria', 'categ', 'WITH', 'e.categoria = categ')
            ->join('App\Entity\Financeiro\CentroCusto', 'cc', 'WITH', 'e.centroCusto = cc')
            ->join('App\Entity\Financeiro\Modo', 'modo', 'WITH', 'e.modo = modo');
    }

    public function findAbertasAnteriores(\DateTime $dtVenctoEfetiva, Carteira $carteira)
    {
        $filterDatas = array(
            new FilterData('dtVenctoEfetiva', 'LT', $dtVenctoEfetiva->setTime(0, 0, 0, 0)),
            new FilterData('carteira', 'EQ', $carteira),
            new FilterData('status', 'IN', ['ABERTA', 'A_COMPENSAR'])
        );
        $orders = array(
            ['column' => 'e.dtVenctoEfetiva', 'dir' => 'asc'],
            ['column' => 'e.valorTotal', 'dir' => 'asc']
        );
        return $this->findByFilters($filterDatas, $orders, 0, 0);
    }

    /**
     * @param \DateTime $dtPagto
     * @param $carteirasIds
     * @param $tipoSaldo
     * @return mixed
     */
    public function findSaldo(\DateTime $dtPagto, $carteirasIds, $tipoSaldo)
    {
        $ql = "SELECT sum(valor_total) as valor_total FROM vw_fin_movimentacao m " .
            "WHERE m.cart_id IN (:carteirasIds) AND " .
            "( (m.status = 'REALIZADA' AND m.dt_pagto <= :dtPagto) ";

        if (in_array($tipoSaldo, ['SALDO_POSTERIOR_COM_CHEQUES', 'SALDO_ANTERIOR_COM_CHEQUES'])) {
            $ql .= "OR (m.status = 'REALIZADA' AND modo_descricao LIKE 'CHEQUE%' AND m.dt_vencto_efetiva <= :dtPagto AND m.dt_pagto > :dtPagto)" .
                " OR (m.status = 'A_COMPENSAR' AND m.dt_vencto_efetiva <= :dtPagto)";
        }
        $ql .= ")";

        $carteirasIds = is_array($carteirasIds) ? $carteirasIds : array($carteirasIds);

        $rsm = new ResultSetMapping ();
        $qry = $this->getEntityManager()->createNativeQuery($ql, $rsm);
        $qry->setParameter('dtPagto', $dtPagto);
        $qry->setParameter('carteirasIds', $carteirasIds);
        $qry->getSQL();
        $qry->getParameters();
        $rsm->addScalarResult('valor_total', 'valor_total');
        $r = $qry->getResult();

        return $r[0]['valor_total'];
    }

    public function findTotal(\DateTime $dtIni, \DateTime $dtFim, ?Carteira $carteira = null, ?Categoria $categoria = null, ?Modo $modo = null, ?OperadoraCartao $operadoraCartao = null)
    {
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim->setTime(23, 59, 59, 999999);

        $params = [];
        $params['dtIni'] = $dtIni;
        $params['dtFim'] = $dtFim;

        $ql = "SELECT SUM( m.valor_total ) as valor_total FROM fin_movimentacao m WHERE m.dt_pagto BETWEEN :dtIni AND :dtFim";

        if ($carteira) {
            $ql .= " AND m.carteira_id = :carteiraId";
            $params['carteiraId'] = $carteira->getId();
        }
        if ($categoria) {
            $ql .= " AND m.categoria_id = :categoriaId";
            $params['categoriaId'] = $categoria->getId();
        }
        if ($modo) {
            $ql .= " AND m.modo_id = :modoId";
            $params['modoId'] = $modo->getId();
        }
        if ($operadoraCartao) {
            $ql .= " AND m.cadeia_id IN (SELECT cadeia_id FROM fin_movimentacao WHERE cadeia_id = m.cadeia_id AND operadora_cartao_id = :operadoraCartaoId)";
            $params['operadoraCartaoId'] = $operadoraCartao->getId();
        }

        $rsm = new ResultSetMapping ();
        $qry = $this->getEntityManager()->createNativeQuery($ql, $rsm);
        $qry->setParameters($params);
        $qry->getSQL();
        $qry->getParameters();
        $rsm->addScalarResult('valor_total', 'valor_total');
        $r = $qry->getResult();
        if ($r) {
            return $r[0]['valor_total'];
        } else {
            return null;
        }
    }


    public function getEntityClass()
    {
        return Movimentacao::class;
    }
}

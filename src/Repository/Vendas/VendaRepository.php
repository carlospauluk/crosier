<?php

namespace App\Repository\Vendas;

use App\Entity\Vendas\Venda;
use App\Repository\FilterRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Venda.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class VendaRepository extends FilterRepository
{

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->leftJoin('App\Entity\RH\Funcionario', 'vendedor', 'WITH', 'e.vendedor = vendedor');
    }

    public function findByDtVendaAndPV(\DateTime $dtVenda, $pv)
    {
        $dtVenda->setTime(0, 0, 0, 0);
        $ql = "SELECT v FROM App\Entity\Vendas\Venda v WHERE v.dtVenda = :dtVenda AND v.pv = :pv";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'dtVenda' => $dtVenda,
            'pv' => $pv
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de uma venda encontrada para [' . $dtVenda . '] e [' . $pv . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }

    public function findByPVAndMesAno($pv, $mesano)
    {
        $ql = "SELECT v FROM App\Entity\Vendas\Venda v WHERE v.mesano = :mesano AND v.pv = :pv";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'mesano' => $mesano,
            'pv' => $pv
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de uma venda encontrada para [' . $pv . '] e [' . $mesano . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }

    public function findByPV($pv)
    {
        $hoje = new \DateTime();
        $mesano = $hoje->format('Ym');
        return $this->findByPVAndMesAno($pv, $mesano);
    }

    public function findTotalAVistaEKT(\DateTime $dtIni, \DateTime $dtFim, $bonsucesso)
    {
        $ql = "SELECT sum(v.valor_total) as total " .
            "FROM ven_venda v, ven_plano_pagto pp, rh_funcionario vendedor " .
            "WHERE " .
            "vendedor.id = v.vendedor_id AND " .
            "v.plano_pagto_id = pp.id " .
            "AND v.pv IS NOT NULL " .
            "AND (" .
            "pp.codigo LIKE '1.00' OR " .
            "pp.codigo LIKE '9.99' OR " .
            "pp.codigo LIKE '3.0' OR " .
            "pp.codigo LIKE '5%' OR " .
            "pp.codigo LIKE '2%') AND " .
            "v.dt_venda BETWEEN :dtIni AND :dtFim " .
            "AND v.deletado = false";

        if ($bonsucesso == true) {
            $ql .= " AND (vendedor.codigo < 90 OR vendedor.codigo = 99)";
        } else {
            $ql .= " AND vendedor.codigo = 95";
        }

        $rsm = new ResultSetMapping();
        $qry = $this->getEntityManager()->createNativeQuery($ql, $rsm);
        $dtIni->setTime(0, 0, 0, 0);
        $qry->setParameter('dtIni', $dtIni);
        $dtFim->setTime(23, 59, 59, 999999);
        $qry->setParameter('dtFim', $dtFim);
        $qry->getSQL();
        $qry->getParameters();
        $rsm->addScalarResult('total', 'total');
        $r = $qry->getResult();
        if ($r) {
            return $r[0]['total'];
        } else {
            return null;
        }
    }

    public function findTotalVendasPorPeriodoVendedores(\DateTime $dtIni, \DateTime $dtFim, $codVendedorIni, $codVendedorFim) {

        $sql = "SELECT vendedor.id, sum(valor_total) FROM ven_venda v, rh_funcionario vendedor " .
            "WHERE v.vendedor_id = vendedor.id AND " .
            "v.dt_venda BETWEEN :dtIni and :dtFim AND " .
            "vendedor.codigo BETWEEN :codVendedorIni AND :codVendedorFim " .
            "GROUP BY v.vendedor_id";

        $rsm = new ResultSetMapping();
        $qry = $this->getEntityManager()->createNativeQuery($ql, $rsm);
        $dtIni->setTime(0, 0, 0, 0);
        $qry->setParameter('dtIni', $dtIni);
        $dtFim->setTime(23, 59, 59, 999999);
        $qry->setParameter('dtFim', $dtFim);
        $qry->getSQL();
        $qry->getParameters();
        $rsm->addScalarResult('total', 'total');
        $r = $qry->getResult();

    }

    public function getEntityClass()
    {
        return Venda::class;
    }
}

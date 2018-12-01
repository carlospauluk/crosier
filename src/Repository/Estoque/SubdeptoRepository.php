<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\Fornecedor;
use App\Entity\Estoque\Subdepto;
use App\Exception\ViewException;
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

    /**
     * @param Fornecedor $fornecedor
     * @return mixed
     * @throws ViewException
     */
    public function getSubdeptosAtivosByFornecedor(Fornecedor $fornecedor) {
        try {
            $dql = "SELECT sd FROM \App\Entity\Estoque\Subdepto sd WHERE sd.id IN (SELECT IDENTITY(p.subdepto) FROM \App\Entity\Estoque\Produto p WHERE p.atual = TRUE AND p.fornecedor = :fornecedor) ORDER BY sd.nome";
            $qry = $this->getEntityManager()->createQuery($dql);
            $qry->setParameter('fornecedor', $fornecedor);
            return $qry->getResult();
        } catch (\Exception $e) {
            throw new ViewException('Erro ao consultar subdeptos ativos do fornecedor');
        }
    }
}

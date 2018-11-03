<?php

namespace App\Repository\Estoque;

use App\Entity\Estoque\Produto;
use App\Repository\FilterRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Repository para a entidade Produto.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class ProdutoRepository extends FilterRepository
{

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->join('App\Entity\Estoque\Fornecedor', 'f', 'WITH', 'e.fornecedor = f')
            ->join('App\Entity\Base\Pessoa', 'fp', 'WITH', 'f.pessoa = fp')
            ;
    }

    public function findByReduzidoEktAndDtVenda($reduzidoEkt, \DateTime $dtVenda)
    {

        $ql = "SELECT p FROM App\Entity\Estoque\Produto p JOIN App\Entity\Estoque\ProdutoReduzidoEktMesano ekt WHERE ekt.produto = p AND p.reduzidoEkt = :reduzidoEkt AND ekt.mesano = :mesano";
        $query = $this->getEntityManager()->createQuery($ql);

        $mesano = $dtVenda->format('Ym');

        $query->setParameters(array(
            'reduzidoEkt' => $reduzidoEkt,
            'mesano' => $mesano
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de um produto encontrado para [' . $reduzidoEkt . '] e [' . $mesano . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }

    public function getEntityClass()
    {
        return Produto::class;
    }
}

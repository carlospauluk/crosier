<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\BandeiraCartao;
use App\Entity\Financeiro\Modo;
use App\Repository\FilterRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Repository para a entidade BandeiraCartao.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class BandeiraCartaoRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return BandeiraCartao::class;
    }

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->join('App\Entity\Financeiro\Modo', 'm', 'WITH', 'e.modo = m');
    }

    public function findByLabelsAndModo($str, Modo $modo)
    {
        $str = strtoupper($str);
        // Primeiro tenta achar por um LIKE no campo labels


        $ql = "SELECT bc FROM BandeiraCartao bc WHERE bc.modo = :modo AND (bc.descricao LIKE :str OR bc.labels LIKE :str)";
        $qry = $this->getEntityManager()->createQuery($ql);
        $qry->setParameter('modo', $modo);
        $qry->setParameter('str', $str);

        $r = $qry->getSingleResult();

        // Senão encontrar, então usa a comparação inversa
        if (!$r) {

            $todas = $this->findAll();

            foreach ($todas as $bc) {
                if ($bc->getModo() == $modo) {

                    $labels = explode("\n", $bc->getLabels());

                    foreach ($labels as $label) {
                        if (stripos($label, $str)) {
                            return $bc;
                        }
                    }
                }
            }
        } else {
            return $r;
        }
    }
}

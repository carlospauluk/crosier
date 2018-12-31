<?php

namespace App\EntityHandler\Financeiro;

use App\Entity\Financeiro\Cadeia;
use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\EntityHandler;
use Doctrine\ORM\Query\ResultSetMapping;

class CadeiaEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Cadeia::class;
    }

    public function beforeSave($cadeia)
    {
        if (!$cadeia->getUnqc()) {
            $cadeia->setUnqc(md5(uniqid(rand(), true)));
        }
    }

    public function corrigirUnqcs() {
        $cadeias = $this->getEntityManager()->getRepository(Cadeia::class)->findAll();
        foreach ($cadeias as $cadeia) {
            $this->save($cadeia);
        }
    }


    public function removerCadeiasComApenasUmaMovimentacao() {
        $rsm = new ResultSetMapping();
        $sql = "select id, cadeia_id, count(cadeia_id) as qt from fin_movimentacao group by cadeia_id having qt < 2";
        $qry = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        $rsm->addScalarResult('id', 'id');
        $rs = $qry->getResult();
        if ($rs) {
            foreach ($rs as $r) {
                $movimentacao = $this->getEntityManager()->find(Movimentacao::class, $r['id']);
                if ($movimentacao->getCadeia()) {
                    $cadeia = $this->getEntityManager()->find(Cadeia::class, $movimentacao->getCadeia());
                    $movimentacao->setCadeia(null);
                    $this->getEntityManager()->remove($cadeia);
                }
            }
        }
        $this->getEntityManager()->flush();
    }


}
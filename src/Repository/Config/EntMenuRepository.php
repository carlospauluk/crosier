<?php

namespace App\Repository\Config;

use App\Entity\Config\EntMenu;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade EntMenu.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class EntMenuRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return EntMenu::class;
    }

    public function makeTree() {
        $ql = "SELECT e FROM App\Entity\Config\EntMenu e WHERE e.pai IS NULL ORDER BY e.ordem";
        $qry = $this->getEntityManager()->createQuery($ql);

        $pais = $qry->getResult();

        $tree = array();

        foreach ($pais as $pai) {
            $tree[] = $pai;
            $this->getFilhos($pai,$tree);
        }
        return $tree;
    }

    private function getFilhos(EntMenu $pai, &$tree) {
        $ql = "SELECT e FROM App\Entity\Config\EntMenu e WHERE e.pai = :pai ORDER BY e.ordem";
        $qry = $this->getEntityManager()->createQuery($ql);
        $qry->setParameter('pai', $pai);
        $rs = $qry->getResult();
        if (count($rs) > 0) {
            foreach ($rs as $r) {
                $tree[] = $r;
                $this->getFilhos($r, $tree);
            }
        } else {
            return;
        }
    }
}

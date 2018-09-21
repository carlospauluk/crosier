<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Categoria;
use App\Repository\FilterRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Banco.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class CategoriaRepository extends FilterRepository
{

    public function buildTreeList()
    {

        $sql = "SELECT id, codigo, concat(rpad('', 2*(length(codigo)-1),'.'), codigo, ' - ',  descricao) as descricaoMontada FROM fin_categoria ORDER BY codigo_ord";

//         $em = $this->getDoctrine()->getEntityManager();
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $r = $stmt->fetchAll();
        return $r;

//         $em = $this->getEntityManager();
//         $qb = $em->createQueryBuilder();
//         $qb->select('e')->from('App\Entity\Financeiro\Categoria', 'e')->orderBy("e.codigoOrd");
//         $query = $qb->getQuery();
//         return $query->execute();
    }

    public function getEntityClass()
    {
        return Categoria::class;
    }
}

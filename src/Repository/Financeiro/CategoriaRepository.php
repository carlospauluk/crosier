<?php
namespace App\Repository\Financeiro;

use App\Entity\Financeiro\Categoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Banco.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class CategoriaRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categoria::class);
    }
    
    public function buildTreeList() {
        
        $sql = "SELECT id, codigo, concat(rpad('', 2*(length(codigo)-1),'.'), codigo, ' - ',  descricao) as descricaoMontada FROM fin_categoria ORDER BY codigo_ord";
        
//         $em = $this->getDoctrine()->getManager();
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
}

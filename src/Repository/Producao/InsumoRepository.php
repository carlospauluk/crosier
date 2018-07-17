<?php
namespace App\Repository\Producao;

use App\Entity\Producao\Insumo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Insumo.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class InsumoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Insumo::class);
    }
    
    public function findPrecoAtual(Insumo $insumo)
    {
        $ql = "SELECT ip FROM App\Entity\Producao\InsumoPreco ip JOIN ip.insumo i WHERE i.id = :insumo_id ORDER BY ip.dtCusto DESC";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'insumo_id' => $insumo->getId()
        ));
        $query->setMaxResults(1);
        return $query->getSingleResult();
    }
    
}

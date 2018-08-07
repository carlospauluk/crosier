<?php
namespace App\Repository\Vendas;

use App\Entity\Vendas\Venda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Venda.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class VendaRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Venda::class);
    }
    
    public function findByDtVendaAndPV(\DateTime $dtVenda, $pv)
    {
        $dtVenda->setTime(0,0,0,0);
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
}

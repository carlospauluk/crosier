<?php
namespace App\Repository\Fiscal;

use App\Entity\Fiscal\NCM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade NCM.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class NCMRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NCM::class);
    }

    public function findByNCM($ncm)
    {
        $ql = "SELECT ncm FROM App\Entity\Fiscal\NCM ncm WHERE ncm.codigo = :ncm";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'ncm' => $ncm
        ));
        
        $results = $query->getResult();
        
        if (count($results) > 1) {
            throw new \Exception('Mais de um NCM encontrado para [' . $ncm . ']');
        }
        
        return count($results) == 1 ? $results[0] : null;
    }
}

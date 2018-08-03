<?php
namespace App\Repository\Producao;

use App\Entity\Producao\Instituicao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Instituicao.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class InstituicaoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Instituicao::class);
    }

    public function findAllByNomes($str)
    {
        $ql = "SELECT e FROM 
                    App\Entity\Producao\Instituicao e LEFT JOIN 
                    e.fornecedor f LEFT JOIN f.pessoa fp LEFT JOIN
                    e.cliente c LEFT JOIN c.pessoa cp 
                    WHERE 
                    (e.nome LIKE :str OR 
                    cp.nome LIKE :str OR 
                    cp.nomeFantasia LIKE :str OR 
                    fp.nome LIKE :str OR 
                    fp.nomeFantasia LIKE :str)";
        
        $qry = $this->getEntityManager()->createQuery($ql);
        $qry->setParameter('str', '%' . $str . '%');
        
        return $qry->getResult();
    }
}

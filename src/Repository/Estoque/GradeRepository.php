<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Grade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Grade.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class GradeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grade::class);
    }

    public function findGradeArray(Grade $grade)
    {
        $ql = "SELECT gt FROM App\Entity\Estoque\GradeTamanho gt JOIN gt.grade g WHERE g.id = :grade_id ORDER BY gt.ordem";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'grade_id' => $grade->getId()
        ));
        $gts = $query->getResult();
        
        $r = array();
        
        foreach ($gts as $gt) {
            $_gt = array();
            $_gt['ordem'] = $gt->getOrdem();
            $_gt['tamanho'] = $gt->getTamanho();
            $r[] = $_gt;
        }
        
        return $r;
    }
}

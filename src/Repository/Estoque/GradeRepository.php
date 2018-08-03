<?php
namespace App\Repository\Estoque;

use App\Entity\Estoque\Grade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Grade.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class GradeRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
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
    
    public function findByGradeCodigoAndTamanho($gradeCodigo, $tamanho) {
        
        $ql = "SELECT gt FROM App\Entity\Estoque\GradeTamanho gt JOIN App\Entity\Estoque\Grade g WHERE gt.grade = g AND g.codigo = :gradeCodigo AND gt.tamanho = :tamanho";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'gradeCodigo' => $gradeCodigo,
            'tamanho' => $tamanho
        ));
        
        $results = $query->getResult();
        
        if (count($results) > 1) {
            throw new \Exception('Mais de um gradeTamanho encontrado para [' . $gradeCodigo . '] e [' . $tamanho . ']');
        }
        
        return count($results) == 1 ? $results[0] : null;
    }
}
